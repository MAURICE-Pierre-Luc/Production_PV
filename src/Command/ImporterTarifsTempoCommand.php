<?php

namespace App\Command;

use App\Entity\GrilleTarifaire;
use App\Enum\CouleurJour;
use App\Enum\TypeHoraire;
use App\Repository\GrilleTarifaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:importer-tarifs-tempo',
    description: 'Récupère et importe les tarifs historiques Tempo depuis la CRE'
)]
class ImporterTarifsTempoCommand extends Command{
    private const URL_CSV = 'https://www.cre.fr/fileadmin/Documents/Open_data/Marches_de_detail/Option_Tempo.csv';

    private const DATE_MINIMUM = '2021-01-01';
    private const PUISSANCES_AUTORISEES = [6, 9, 12];

    public function __construct(private EntityManagerInterface $entityManager, private GrilleTarifaireRepository $grilleTarifaireRepository) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int {
        $io = new SymfonyStyle($input, $output);

        $io->section('Téléchargement du CSV de la CRE');

        $curl = curl_init(self::URL_CSV);

        if ($curl === false) {
            $io->error('Impossible d' . "'" . 'initialiser cURL.');

            return Command::FAILURE;
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'Symfony-Tempo-Importer/1.0',
        ]);

        $contenu = curl_exec($curl);
        $codeHTTP = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $erreurCurl = curl_error($curl);

        curl_close($curl);

        if ($contenu === false || $codeHTTP !== 200) {
            $io->error('Échec du téléchargement : '. ($erreurCurl !== '' ? $erreurCurl : 'code HTTP ' . $codeHTTP));

            return Command::FAILURE;
        }

        if ($contenu === '') {
            $io->error('Le fichier téléchargé est vide.');

            return Command::FAILURE;
        }

        $handle = fopen('php://memory', 'r+');

        if ($handle === false) {
            $io->error('Impossible de créer le flux mémoire.');

            return Command::FAILURE;
        }

        fwrite($handle, $contenu);
        rewind($handle);

        unset($contenu);

        $entetes = fgetcsv($handle, 0, ';', '"', '');

        if ($entetes === false) {
            fclose($handle);

            $io->error('Impossible de lire l\'en-tête du CSV.');

            return Command::FAILURE;
        }

        // Supprimer un éventuel BOM UTF-8.
        $entetes[0] = preg_replace('/^\xEF\xBB\xBF/', '', $entetes[0]);

        $colonnes = array_flip($entetes);

        $colonnesObligatoires = [
            'DATE_DEBUT',
            'DATE_FIN',
            'P_SOUSCRITE',
            'PART_VARIABLE_HCBleu_TTC',
            'PART_VARIABLE_HPBleu_TTC',
            'PART_VARIABLE_HCBlanc_TTC',
            'PART_VARIABLE_HPBlanc_TTC',
            'PART_VARIABLE_HCRouge_TTC',
            'PART_VARIABLE_HPRouge_TTC',
        ];

        foreach ($colonnesObligatoires as $colonne) {
            if (!isset($colonnes[$colonne])) {
                fclose($handle);

                $io->error( sprintf('La colonne "%s" est absente du CSV.', $colonne));

                return Command::FAILURE;
            }
        }

        $colonnesTarifs = [
            'PART_VARIABLE_HCBleu_TTC' => ['BLEU', 'HC'],
            'PART_VARIABLE_HPBleu_TTC' => ['BLEU', 'HP'],
            'PART_VARIABLE_HCBlanc_TTC' => ['BLANC', 'HC'],
            'PART_VARIABLE_HPBlanc_TTC' => ['BLANC', 'HP'],
            'PART_VARIABLE_HCRouge_TTC' => ['ROUGE', 'HC'],
            'PART_VARIABLE_HPRouge_TTC' => ['ROUGE', 'HP'],
        ];

        $plages = [
            ['00:00:00', '06:00:00', 'HC'],
            ['06:00:00', '22:00:00', 'HP'],
            ['22:00:00', '00:00:00', 'HC'],
        ];

        $dateMinimum = new \DateTime(self::DATE_MINIMUM);
        $aujourdhui = new \DateTime('today');

        $ajoutes = 0;
        $misAJour = 0;
        $ignores = 0;
        $numeroLigne = 1;

        $connexion = $this->entityManager->getConnection();

        try {
            $connexion->beginTransaction();

            while (($ligne = fgetcsv($handle, 0, ';', '"', '')) !== false) {

                $numeroLigne++;

                if ($ligne === [null] || $ligne === []) {
                    $ignores++;
                    continue;
                }

                $dateDebutTexte = trim($ligne[$colonnes['DATE_DEBUT']] ?? '');

                $dateFinTexte = trim($ligne[$colonnes['DATE_FIN']] ?? '');

                $puissanceTexte = trim($ligne[$colonnes['P_SOUSCRITE']] ?? '');

                $dateDebut = \DateTime::createFromFormat('!d/m/Y', $dateDebutTexte);

                if ($dateFinTexte === '') {
                    $dateFin = clone $aujourdhui;
                } else {
                    $dateFin = \DateTime::createFromFormat('!d/m/Y', $dateFinTexte);
                }

                if ($dateDebut === false || $dateFin === false || !is_numeric($puissanceTexte)) {
                    $io->warning(sprintf('Ligne %d ignorée : dates ou puissance invalides.', $numeroLigne));

                    $ignores++;
                    continue;
                }

                if ($dateFin < $dateMinimum) {
                    $ignores++;
                    continue;
                }

                $puissance = (int) $puissanceTexte;

                if (!in_array($puissance,self::PUISSANCES_AUTORISEES,true)) {
                    $ignores++;
                    continue;
                }

                foreach ($colonnesTarifs as $colonne => [$couleur, $type]) {
                    $tarifTexte = trim($ligne[$colonnes[$colonne]] ?? '');

                    // Ignorer les tarifs vides.
                    if ($tarifTexte === '') {
                        continue;
                    }

                    // Convertir la virgule décimale en point.
                    $tarif = str_replace(',', '.', $tarifTexte);

                    if (!is_numeric($tarif)) {
                        $io->warning(sprintf('Ligne %d : tarif invalide dans %s.', $numeroLigne, $colonne));

                        continue;
                    }

                    $couleurEnum = CouleurJour::from($couleur);
                    $typeEnum = TypeHoraire::from($type);

                    foreach ($plages as [$deb, $fin, $typePlage]) {
                        // Ne garder que les plages du bon type.
                        if ($typePlage !== $type) {
                            continue;
                        }

                        $heureDebut = \DateTime::createFromFormat('!H:i:s', $deb);

                        $heureFin = \DateTime::createFromFormat('!H:i:s', $fin);

                        if ($heureDebut === false || $heureFin === false) {
                            throw new \RuntimeException(
                                'Impossible de convertir une plage horaire.'
                            );
                        }

                        $tarifExistant = $this
                            ->grilleTarifaireRepository
                            ->findOneBy([
                                'dateDebut' => $dateDebut,
                                'couleur' => $couleurEnum,
                                'deb' => $heureDebut,
                                'fin' => $heureFin,
                                'type' => $typeEnum,
                                'puissance' => $puissance,
                            ]);

                        if ($tarifExistant !== null) {

                            $tarifExistant
                                ->setTarif($tarif)
                                ->setDateFin($dateFin);
                            $misAJour++;
                            
                        } else {

                            $grilleTarifaire = new GrilleTarifaire();

                            $grilleTarifaire
                                ->setDateDebut($dateDebut)
                                ->setDateFin($dateFin)
                                ->setCouleur($couleurEnum)
                                ->setDeb($heureDebut)
                                ->setFin($heureFin)
                                ->setType($typeEnum)
                                ->setPuissance($puissance)
                                ->setTarif($tarif);

                            $this->entityManager->persist($grilleTarifaire);

                            $ajoutes++;
                        }
                    }
                }
            }

            $this->entityManager->flush();

            $connexion->commit();
        } catch (\Throwable $e) {

            if ($connexion->isTransactionActive()) {
                $connexion->rollBack();
            }

            fclose($handle);

            $io->error( sprintf('Échec de l’import à la ligne %d : %s', $numeroLigne, $e->getMessage()));

            return Command::FAILURE;
        }

        fclose($handle);

        $io->success(sprintf('Import terminé : %d lignes créées, %d mises à jour, %d lignes ignorées.', $ajoutes, $misAJour, $ignores));

        return Command::SUCCESS;
    }
}
