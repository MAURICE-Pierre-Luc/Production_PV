<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261014133818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jour ADD energie_consommee_hc NUMERIC(15, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE jour ADD energie_consommee_hp NUMERIC(15, 3) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jour DROP energie_consommee_hc');
        $this->addSql('ALTER TABLE jour DROP energie_consommee_hp');
    }
}
