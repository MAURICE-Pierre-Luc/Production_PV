<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261008085751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batterie_jour DROP CONSTRAINT fk_9cf39679fac95031');
        $this->addSql('ALTER TABLE consommation_jour DROP CONSTRAINT fk_31982fd2fac95031');
        $this->addSql('ALTER TABLE production_jour DROP CONSTRAINT fk_10792d1fac95031');
        $this->addSql('ALTER TABLE ve_jour DROP CONSTRAINT fk_bfdfa45fac95031');
        $this->addSql('DROP TABLE batterie_jour');
        $this->addSql('DROP TABLE consommation_jour');
        $this->addSql('DROP TABLE production_jour');
        $this->addSql('DROP TABLE ve_jour');
        $this->addSql('ALTER TABLE grille_tarifaire ALTER type TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE grille_tarifaire ALTER type SET NOT NULL');
        $this->addSql('ALTER TABLE jour ADD production_energie_hc NUMERIC(15, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD production_energie_hp NUMERIC(15, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD production_puissance_max NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD production_heure_puissance_max TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD energie_importee_hc NUMERIC(15, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD energie_importee_hp NUMERIC(15, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD soc_min NUMERIC(5, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD soc_max NUMERIC(5, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD soh NUMERIC(5, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD temperature_min NUMERIC(5, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD temperature_max NUMERIC(5, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD nb_alarmes INT DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD ve_energie_hc NUMERIC(15, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD ve_energie_hp NUMERIC(15, 3) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batterie_jour (soc_min NUMERIC(5, 2) DEFAULT NULL, soc_max NUMERIC(5, 2) DEFAULT NULL, soh NUMERIC(5, 2) DEFAULT NULL, temperature_min NUMERIC(5, 2) DEFAULT NULL, temperature_max NUMERIC(5, 2) DEFAULT NULL, nb_alarmes INT DEFAULT NULL, date_jour DATE NOT NULL, PRIMARY KEY (date_jour))');
        $this->addSql('CREATE TABLE consommation_jour (energie_importee_hc NUMERIC(15, 3) DEFAULT NULL, energie_importee_hp NUMERIC(15, 3) DEFAULT NULL, date_jour DATE NOT NULL, PRIMARY KEY (date_jour))');
        $this->addSql('CREATE TABLE production_jour (energie_hc NUMERIC(15, 3) DEFAULT NULL, energie_hp NUMERIC(15, 3) DEFAULT NULL, puissance_max NUMERIC(10, 2) DEFAULT NULL, heure_puissance_max TIME(0) WITHOUT TIME ZONE DEFAULT NULL, date_jour DATE NOT NULL, PRIMARY KEY (date_jour))');
        $this->addSql('CREATE TABLE ve_jour (energie_hc NUMERIC(15, 3) DEFAULT NULL, energie_hp NUMERIC(15, 3) DEFAULT NULL, date_jour DATE NOT NULL, PRIMARY KEY (date_jour))');
        $this->addSql('ALTER TABLE batterie_jour ADD CONSTRAINT fk_9cf39679fac95031 FOREIGN KEY (date_jour) REFERENCES jour (date_jour) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE consommation_jour ADD CONSTRAINT fk_31982fd2fac95031 FOREIGN KEY (date_jour) REFERENCES jour (date_jour) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE production_jour ADD CONSTRAINT fk_10792d1fac95031 FOREIGN KEY (date_jour) REFERENCES jour (date_jour) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ve_jour ADD CONSTRAINT fk_bfdfa45fac95031 FOREIGN KEY (date_jour) REFERENCES jour (date_jour) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grille_tarifaire ALTER type TYPE NUMERIC(10, 4)');
        $this->addSql('ALTER TABLE grille_tarifaire ALTER type DROP NOT NULL');
        $this->addSql('ALTER TABLE jour DROP production_energie_hc');
        $this->addSql('ALTER TABLE jour DROP production_energie_hp');
        $this->addSql('ALTER TABLE jour DROP production_puissance_max');
        $this->addSql('ALTER TABLE jour DROP production_heure_puissance_max');
        $this->addSql('ALTER TABLE jour DROP energie_importee_hc');
        $this->addSql('ALTER TABLE jour DROP energie_importee_hp');
        $this->addSql('ALTER TABLE jour DROP soc_min');
        $this->addSql('ALTER TABLE jour DROP soc_max');
        $this->addSql('ALTER TABLE jour DROP soh');
        $this->addSql('ALTER TABLE jour DROP temperature_min');
        $this->addSql('ALTER TABLE jour DROP temperature_max');
        $this->addSql('ALTER TABLE jour DROP nb_alarmes');
        $this->addSql('ALTER TABLE jour DROP ve_energie_hc');
        $this->addSql('ALTER TABLE jour DROP ve_energie_hp');
    }
}
