<?php

namespace App\Command;

use App\Enum\CouleurJour;
use App\Entity\GrilleTarifaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Importe la grille tarifaire Option Tempo depuis le CSV open-data de la CRE
 * et alimente GRILLE_TARIFAIRE (3 lignes par période : Bleu / Blanc / Rouge).
 *
 * Source : https://www.cre.fr/documents/open-data/historique-des-tarifs-reglementes-de-vente-delectricite-pour-les-consommateurs-residentiels.html
 *
 * Notes :
 * - Les heures creuses Tempo sont fixes et nationales (22h-6h), donc codées en dur ici,
 *   elles ne viennent pas du CSV.
 * - Les prix TTC dépendent de la puissance souscrite (kVA) : filtrable via --puissance.
 * - Pour 6 kVA, le CSV n'a des valeurs exploitables qu'à partir du 01/02/2023
 *   (colonnes vides avant, l'option Tempo n'était pas ouverte à cette puissance).
 */
#[AsCommand(
    name: 'app:import-tarifs-tempo',
    description: 'Importe la grille tarifaire Tempo depuis le CSV open-data de la CRE',
)]
class ImportTarifsTempoCommand extends Command
{
    private const CSV_URL = 'https://www.cre.fr/fileadmin/Documents/Open_data/Marches_de_detail/Option_Tempo.csv';
    private const DEB_HC = '22:00:00';
    private const DEB_HP = '06:00:00';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('puissance', 'p', InputOption::VALUE_REQUIRED, 'Puissance souscrite en kVA', '6')
            ->addOption('depuis', null, InputOption::VALUE_REQUIRED, 'Ne garder que les périodes débutant à partir de cette date (Y-m-d)', '2020-01-01')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $puissance = (int) $input->getOption('puissance');
        $depuis = new \DateTimeImmutable($input->getOption('depuis'));

        $response = $this->httpClient->request('GET', self::CSV_URL);
        $content = $response->getContent();

        // Le fichier est encodé en UTF-8 avec BOM et utilise ';' comme séparateur.
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        $headerLine = array_shift($lines);
        $headers = explode(';', $headerLine);
        $columnIndex = array_flip($headers); // nom de colonne => index

        $requiredColumns = [
            'DATE_DEBUT', 'DATE_FIN', 'P_SOUSCRITE',
            'PART_VARIABLE_HCBleu_TTC', 'PART_VARIABLE_HPBleu_TTC',
            'PART_VARIABLE_HCBlanc_TTC', 'PART_VARIABLE_HPBlanc_TTC',
            'PART_VARIABLE_HCRouge_TTC', 'PART_VARIABLE_HPRouge_TTC',
        ];
        foreach ($requiredColumns as $name) {
            if (!isset($columnIndex[$name])) {
                throw new \RuntimeException(sprintf('Colonne "%s" introuvable dans le CSV de la CRE.', $name));
            }
        }

        $col = fn (array $row, string $name): string => $row[$columnIndex[$name]] ?? '';

        $nbCreated = 0;
        $nbSkippedEmpty = 0;
        $nbSkippedPuissance = 0;
        $nbSkippedDate = 0;

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            $cols = explode(';', $line);

            $puissanceLigne = (int) $col($cols, 'P_SOUSCRITE');
            if ($puissanceLigne !== $puissance) {
                $nbSkippedPuissance++;
                continue;
            }

            $dateDebut = \DateTime::createFromFormat('d/m/Y', $col($cols, 'DATE_DEBUT'));
            if ($dateDebut < $depuis) {
                $nbSkippedDate++;
                continue;
            }
            $dateFinBrute = $col($cols, 'DATE_FIN');
            $dateFin = $dateFinBrute !== '' ? \DateTime::createFromFormat('d/m/Y', $dateFinBrute) : null;

            $prix = [
                'BLEU' => ['hc' => $col($cols, 'PART_VARIABLE_HCBleu_TTC'), 'hp' => $col($cols, 'PART_VARIABLE_HPBleu_TTC')],
                'BLANC' => ['hc' => $col($cols, 'PART_VARIABLE_HCBlanc_TTC'), 'hp' => $col($cols, 'PART_VARIABLE_HPBlanc_TTC')],
                'ROUGE' => ['hc' => $col($cols, 'PART_VARIABLE_HCRouge_TTC'), 'hp' => $col($cols, 'PART_VARIABLE_HPRouge_TTC')],
            ];

            foreach ($prix as $couleur => $valeurs) {
                if ($valeurs['hc'] === '' || $valeurs['hp'] === '') {
                    // Puissance non éligible à Tempo sur cette période (colonnes vides dans le CSV)
                    $nbSkippedEmpty++;
                    continue;
                }

                $tarifHc = (float) str_replace(',', '.', $valeurs['hc']);
                $tarifHp = (float) str_replace(',', '.', $valeurs['hp']);

                $existing = $this->em->getRepository(GrilleTarifaire::class)->findOneBy([
                    'dateDebut' => $dateDebut,
                    'couleur' => CouleurJour::from($couleur),
                ]);

                $grille = $existing ?? new GrilleTarifaire();
                $grille->setDateDebut($dateDebut);
                $grille->setDateFin($dateFin);
                $grille->setCouleur(CouleurJour::from($couleur));
                $grille->setDebHc(new \DateTime(self::DEB_HC));
                $grille->setTarifHc($tarifHc);
                $grille->setDebHp(new \DateTime(self::DEB_HP));
                $grille->setTarifHp($tarifHp);

                $this->em->persist($grille);
                $nbCreated++;
            }
        }

        $this->em->flush();

        $io->success(sprintf(
            '%d lignes de grille tarifaire importées/mises à jour (%d kVA, depuis %s).',
            $nbCreated,
            $puissance,
            $depuis->format('Y-m-d')
        ));
        $io->note(sprintf(
            '%d lignes ignorées (autre puissance), %d ignorées (avant la date de départ), %d ignorées (Tempo non ouvert à cette puissance sur la période).',
            $nbSkippedPuissance,
            $nbSkippedDate,
            $nbSkippedEmpty
        ));

        return Command::SUCCESS;
    }
}
