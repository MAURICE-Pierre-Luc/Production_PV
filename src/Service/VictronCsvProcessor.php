<?php

namespace App\Service;

use App\Repository\ImportRepository;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeInterface;

use App\Repository\GrilleTarifaireRepository;
use App\Repository\JourRepository;

use App\Entity\Jour;
use App\Entity\ProductionJour;
use App\Entity\ConsommationJour;
use App\Entity\BatterieJour;
use App\Entity\VeJour;

class VictronCsvProcessor
{

    private string $projectDir;

    private ?string $previousDate = null;

    private ?string $currentDate = null;

    private ?string $currentHourtype = null;

    private ?float $puissanceAc = null;
    
    private ?float $puissanceDc = null;

    private ?float $previousUserYield = null;
    
    private ?float $previousL1Energy = null;

    private ?float $previousGridPower = null;

    private ?float $previousForwardEnergy = null;

    private ?\DateTimeImmutable $previousTimestamp = null;

    private ?\DateTimeImmutable $dateDebut = null;

    private ?\DateTimeImmutable $dateFin = null;

    private ?\DateTimeZone $fuseau = null;

    private array $donneesJour;

    private array $plagesHoraires = [];

    private array $colonnesVoulues = [ 
        // Horodatage
        /*[
            'source' => 'timestamp',
            'mesure' => 'Europe/Paris',
            'cible' => 'aucune',
        ],*/
        // PRODUCTION_JOUR
        [
            'source' => 'Solar Charger [279]',
            'mesure' => 'User yield',
            'cible' => 'energie_prod',
        ],
        [
            'source' => 'PV Inverter [20]',
            'mesure' => 'L1 Energy',
            'cible' => 'energie_prod',
        ],
        [
            'source' => 'System overview [0]',
            'mesure' => 'PV - AC-coupled on output L1',
            'cible' => 'puissance_prod',
        ],
        [
            'source' => 'System overview [0]',
            'mesure' => 'PV - DC-coupled',
            'cible' => 'puissance_prod',
        ],

        // CONSOMMATION_JOUR
        [
            'source' => 'System overview [0]',
            'mesure' => 'Grid L1',
            'cible' => 'energie_importe',
        ],

        // BATTERIE_JOUR
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'State of charge',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'State of health',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'Battery temperature',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'Low voltage alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'Low battery temperature alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'High battery temperature alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'Cell Imbalance alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'High charge current alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'High discharge current alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'High charge temperature alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'Low charge temperature alarm',
            'cible' => 'batterie',
        ],
        [
            'source' => 'Battery Monitor [512]',
            'mesure' => 'Internal Failure',
            'cible' => 'batterie',
        ],
        // VE_JOUR
        [
            'source' => 'EV Charging Station [40]',
            'mesure' => 'Forward energy',
            'cible' => 'ev',
        ],
    ];

    public function __construct(private ImportRepository $importRepository, KernelInterface $kernel, 
    private GrilleTarifaireRepository $grilleTarifaireRepository, private JourRepository $jourRepository, private EntityManagerInterface $entityManager) {

        $this->projectDir = $kernel->getProjectDir();
        $this->donneesJour = $this->resetDonnesJour();
    }

    private function resetDonnesJour(): array{
        return [
            'batterie' => [
                'soc_min' => null,
                'soc_max' => null,
                'soh' => null,
                'temp_min' => null,
                'temp_max' => null,
                'nb_alarmes' => 0,
            ],
            'consommation' => [
                'energie_importe_hc' => 0,
                'energie_importe_hp' => 0,
            ],
            'production' => [
                'energie_prod_hp' => 0,
                'energie_prod_hc' => 0,
                'puissance_max' => 0,
                'heure_puissance_max' => null,
            ],
            've' => [
                'energie_hc' => 0,
                'energie_hp' => 0,
            ],
        ];
    }

    private function resetEtat(): void{
        $this->previousDate = null;
        $this->currentDate = null;
        $this->currentHourtype = null;
        $this->puissanceAc = null;
        $this->puissanceDc = null;
        $this->previousUserYield = null;
        $this->previousL1Energy = null;
        $this->previousGridPower = null;
        $this->previousForwardEnergy = null;
        $this->previousTimestamp = null;
        $this->dateDebut = null;
        $this->dateFin = null;
        $this->plagesHoraires = [];
        $this->donneesJour = $this->resetDonnesJour();
    }

    private function getFilePath(int $importId): string{
        
        return $this->projectDir . '/var/imports/' . $importId . '.csv';
    }

    private function getPlagesHoraires(string $date): array{

        return $this->grilleTarifaireRepository->trouverPlagesHoraires(new \DateTimeImmutable($date));
    }

    private function getHourType ($plages, $heure): string{
        foreach ($plages as $plage) {
            if ( $heure >= $plage['deb'] && $heure < $plage['fin']) {
                return $plage['type'];
            }
        }

        return 'HP';
    }

    private function extractDateTime(array $ligne, int $indexHorodatage): ?\DateTimeImmutable {

        $horodatage = $ligne[$indexHorodatage] ?? null;

        if ($horodatage === null || $horodatage === '') {
            return null;
        }

        $this->fuseau ??= new \DateTimeZone('Europe/Paris');

        return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $horodatage, $this->fuseau) ?: null;
    }

    private function getColonnesIndex(array $sources, array $mesures): array{

        $colonnesRetenues = [];

        foreach ($sources as $index => $source) { 
            $mesure = $mesures[$index] ?? null;

            foreach ($this->colonnesVoulues as $colonneVoulue) {

                if ( $source === $colonneVoulue['source'] && $mesure === $colonneVoulue['mesure'] ) {
                    $colonnesRetenues[$index] = $colonneVoulue;
                    break;
                } 
            }
        }
        return $colonnesRetenues;

    }

    public function traiterCSV(int $importId): array {

        $chemin = $this->getFilePath($importId);

        if (!is_readable($chemin)) {
            throw new \RuntimeException('Impossible d\'ouvrir le fichier CSV.');
        }

        $fichier = fopen($chemin, 'r');

        if ($fichier === false) {
            throw new \RuntimeException('Impossible d\'ouvrir le fichier CSV.');
        }

        $sources = fgetcsv($fichier); // On charge la ligne des sources de meusure des données

        $mesures = fgetcsv($fichier); // On charge la ligne des mesures


        //Si il nous manque l'une ou l'autres des lignes on ne peut pas identifier les colonnes correctement donc on annule le traitement du fichier
        if ($sources === false || $mesures === false) {
            fclose($fichier); 
            throw new \RuntimeException( 'Le fichier CSV ne contient pas les en-têtes attendues.' ); 
        }

        $colonnesRetenues = $this->getColonnesIndex($sources, $mesures);

        if (count($colonnesRetenues) !== count($this->colonnesVoulues)) {
            fclose($fichier);

            throw new \RuntimeException(
                'Certaines colonnes nécessaires sont absentes du CSV.'
            );
        }

        fgetcsv($fichier); // On saute la ligne avec les unités

        $this->resetEtat();

        $indexHorodatage = 0 ;
        
        while (($ligne = fgetcsv($fichier)) !== false) {

            $timestamp = $this->extractDateTime($ligne, $indexHorodatage);

            $this->puissanceAc = null;
            $this->puissanceDc = null;  

            if ($timestamp === null) {
                continue;
            }

            $this->currentDate = $timestamp->format('Y-m-d');
            $this->dateFin = $timestamp;

            if ($this->dateDebut === null) {
                $this->dateDebut = $timestamp;
            }

            $heure = $timestamp->format('H:i:s');

            if ($this->previousDate === null) {
                
                $this->previousDate = $this->currentDate;

                $this->plagesHoraires = $this->getPlagesHoraires($this->currentDate);
            }
            elseif ($this->currentDate !== $this->previousDate) {

                $this->sauvegarderJour();

                $this->resetEtat();

                $this->previousDate = $this->currentDate;

                $this->plagesHoraires = $this->getPlagesHoraires($this->currentDate);

                $this->previousUserYield = null;
                $this->previousL1Energy = null;
                $this->previousForwardEnergy = null;
                $this->previousGridPower = null;
                $this->previousTimestamp = null;
            }

            $this->currentHourtype = $this->getHourType( $this->plagesHoraires, $heure);

            foreach ($colonnesRetenues as $index => $colonneInfo) {

                $valeur = $ligne[$index] ?? null;

                if ($valeur === null || $valeur === '') {
                    continue;
                }

                if ($colonneInfo['cible'] === 'energie_prod') {
                    $this->traiterProduction( $colonneInfo['source'], (float) $valeur, $this->currentHourtype );
                }
                elseif ($colonneInfo['cible'] === 'puissance_prod') {
                    $this->traiterPuissanceProduction($colonneInfo['mesure'], (float) $valeur, $heure);
                }
                elseif ($colonneInfo['cible'] === 'energie_importe') {
                    $this->traiterConsommation( (float) $valeur, $timestamp, $this->currentHourtype );
                }
                elseif ($colonneInfo['cible'] === 'batterie') {
                    $this->traiterBatterie( $colonneInfo['mesure'], $valeur);
                }
                elseif ($colonneInfo['cible'] === 'ev') { 
                    $this->traiterVE( (float) $valeur, $this->currentHourtype);
                }
            }
        }

        if ($this->previousDate !== null) {
            $this->sauvegarderJour();
        }

        fclose($fichier);

        return [
            'dateDebut' => $this->dateDebut,
            'dateFin' => $this->dateFin,
        ];
    }

    private function traiterProduction(string $source, float $valeur, ?string $typeHeure): void{

        if ($source === 'Solar Charger [279]') {

            if ($this->previousUserYield !== null) {

                $difference = $valeur - $this->previousUserYield;

                if ($difference >= 0) {

                    if ($typeHeure === 'HC') {
                        $this->donneesJour['production']['energie_prod_hc'] += $difference;
                    } elseif ($typeHeure === 'HP') {
                        $this->donneesJour['production']['energie_prod_hp'] += $difference;
                    }
                }
            }

            $this->previousUserYield = $valeur;
        }

        elseif ($source === 'PV Inverter [20]') {

            if ($this->previousL1Energy !== null) {

                $difference = $valeur - $this->previousL1Energy;

                if ($difference >= 0) {

                    if ($typeHeure === 'HC') {
                        $this->donneesJour['production']['energie_prod_hc'] += $difference;
                    } elseif ($typeHeure === 'HP') {
                        $this->donneesJour['production']['energie_prod_hp'] += $difference;
                    }
                }
            }

            $this->previousL1Energy = $valeur;
        }
    }

    private function traiterPuissanceProduction(string $mesure, float $valeur, string $heure): void {

        if ($mesure === 'PV - AC-coupled on output L1') {
            $this->puissanceAc = $valeur;
        } elseif ($mesure === 'PV - DC-coupled') {
            $this->puissanceDc = $valeur;
        }

        if ($this->puissanceAc !== null && $this->puissanceDc !== null) {
            $puissanceTotale = $this->puissanceAc + $this->puissanceDc;

            if ($puissanceTotale > $this->donneesJour['production']['puissance_max']) {
                $this->donneesJour['production']['puissance_max'] = $puissanceTotale;
                $this->donneesJour['production']['heure_puissance_max'] = $heure;
            }
        }
    }

    private function traiterConsommation(float $puissance, \DateTimeImmutable $timestamp, ?string $typeHeure): void{

        if ($this->previousGridPower !== null && $this->previousTimestamp !== null) {

            $dureeSecondes = $timestamp->getTimestamp() - $this->previousTimestamp->getTimestamp();

            if ($dureeSecondes > 0) {

                $puissancePrecedente = max(0, $this->previousGridPower);
                $puissanceActuelle = max(0, $puissance);

                $puissanceMoyenne = ( $puissancePrecedente + $puissanceActuelle ) / 2;

                // Puissance en W -> énergie en kWh
                $energie = ($puissanceMoyenne * $dureeSecondes) / 3600000;

                if ($typeHeure === 'HC') {

                    $this->donneesJour['consommation']['energie_importe_hc'] += $energie;

                } elseif ($typeHeure === 'HP') {

                    $this->donneesJour['consommation']['energie_importe_hp'] += $energie;
                }
            }
        }

        $this->previousGridPower = $puissance;
        $this->previousTimestamp = $timestamp;
    }

    private function traiterBatterie(string $mesure, mixed $valeur): void{

        switch ($mesure) {
            case 'State of charge':
                $soc = (float) $valeur;

                if (
                    $this->donneesJour['batterie']['soc_min'] === null
                    || $soc < $this->donneesJour['batterie']['soc_min']
                ) {
                    $this->donneesJour['batterie']['soc_min'] = $soc;
                }

                if (
                    $this->donneesJour['batterie']['soc_max'] === null
                    || $soc > $this->donneesJour['batterie']['soc_max']
                ) {
                    $this->donneesJour['batterie']['soc_max'] = $soc;
                }
                break;

            case 'State of health':
                $this->donneesJour['batterie']['soh'] = (float) $valeur;
                break;

            case 'Battery temperature':
                $temperature = (float) $valeur;

                if ($this->donneesJour['batterie']['temp_min'] === null|| $temperature < $this->donneesJour['batterie']['temp_min']) {
                    $this->donneesJour['batterie']['temp_min'] = $temperature;
                }
                if ($this->donneesJour['batterie']['temp_max'] === null || $temperature > $this->donneesJour['batterie']['temp_max']) {
                    $this->donneesJour['batterie']['temp_max'] = $temperature;
                }
                break;

            default:
                // Colonne d'alarme
                if ($valeur !== 'No alarm') {
                    $this->donneesJour['batterie']['nb_alarmes']++;
                }
                break;
        }
    }

    private function traiterVE( float $forwardEnergy, ?string $typeHeure ): void {
        
        if ($this->previousForwardEnergy !== null) {

            $difference = $forwardEnergy - $this->previousForwardEnergy;

            if ($difference >= 0) {
                if ($typeHeure === 'HC') {
                    $this->donneesJour['ve']['energie_hc'] += $difference;
                } elseif ($typeHeure === 'HP') {
                    $this->donneesJour['ve']['energie_hp'] += $difference;
                }
            }
        }

        $this->previousForwardEnergy = $forwardEnergy;
    }

    private function sauvegarderJour(): void{

        $date = \DateTime::createFromFormat('!Y-m-d', $this->previousDate);

        if ($date === false) {
            throw new \RuntimeException(
                'Date invalide : ' . $this->previousDate
            );
        }

        $jour = $this->entityManager
            ->createQuery('SELECT j FROM App\Entity\Jour j WHERE j.dateJour = :d')
            ->setParameter('d', $date, \Doctrine\DBAL\Types\Types::DATE_MUTABLE)
            ->getOneOrNullResult();

        error_log(sprintf('[import] jour=%s existant=%s', $date->format('Y-m-d'), $jour ? 'oui' : 'non'));

        if ($jour === null) {
            $jour = new Jour();
            $jour->setDateJour($date);
        }

        // Production solaire
        $jour->setProductionEnergieHC(
            $this->donneesJour['production']['energie_prod_hc'] !== null
                ? (string) $this->donneesJour['production']['energie_prod_hc']
                : null
        );

        $jour->setProductionEnergieHP(
            $this->donneesJour['production']['energie_prod_hp'] !== null
                ? (string) $this->donneesJour['production']['energie_prod_hp']
                : null
        );

        $jour->setProductionPuissanceMax(
            $this->donneesJour['production']['puissance_max'] !== null
                ? (string) $this->donneesJour['production']['puissance_max']
                : null
        );

        if ($this->donneesJour['production']['heure_puissance_max'] !== null) {
            $heure = \DateTime::createFromFormat(
                '!H:i:s',
                $this->donneesJour['production']['heure_puissance_max']
            );

            if ($heure !== false) {
                $jour->setProductionHeurePuissanceMax($heure);
            }
        }

        // Consommation du réseau
        $jour->setEnergieImporteeHC(
            $this->donneesJour['consommation']['energie_importe_hc'] !== null
                ? (string) $this->donneesJour['consommation']['energie_importe_hc']
                : null
        );

        $jour->setEnergieImporteeHP(
            $this->donneesJour['consommation']['energie_importe_hp'] !== null
                ? (string) $this->donneesJour['consommation']['energie_importe_hp']
                : null
        );

        // Batterie
        $jour->setSocMin(
            $this->donneesJour['batterie']['soc_min'] !== null
                ? (string) $this->donneesJour['batterie']['soc_min']
                : null
        );

        $jour->setSocMax(
            $this->donneesJour['batterie']['soc_max'] !== null
                ? (string) $this->donneesJour['batterie']['soc_max']
                : null
        );

        $jour->setSoh(
            $this->donneesJour['batterie']['soh'] !== null
                ? (string) $this->donneesJour['batterie']['soh']
                : null
        );

        $jour->setTemperatureMin(
            $this->donneesJour['batterie']['temp_min'] !== null
                ? (string) $this->donneesJour['batterie']['temp_min']
                : null
        );

        $jour->setTemperatureMax(
            $this->donneesJour['batterie']['temp_max'] !== null
                ? (string) $this->donneesJour['batterie']['temp_max']
                : null
        );

        $jour->setNbAlarmes(
            $this->donneesJour['batterie']['nb_alarmes']
        );

        // Recharge du véhicule électrique
        $jour->setVeEnergieHC(
            $this->donneesJour['ve']['energie_hc'] !== null
                ? (string) $this->donneesJour['ve']['energie_hc']
                : null
        );

        $jour->setVeEnergieHP(
            $this->donneesJour['ve']['energie_hp'] !== null
                ? (string) $this->donneesJour['ve']['energie_hp']
                : null
        );

        error_log(sprintf('Sauvegarde %s : %s', $this->previousDate, $jour->getDateJour() ? 'existant ou nouveau' : '?'));

        $this->entityManager->persist($jour);
        $this->entityManager->flush();
    }

}
