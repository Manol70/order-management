<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419003540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD mosquito_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993981E434756 FOREIGN KEY (mosquito_id) REFERENCES mosquito (id)');
        $this->addSql('CREATE INDEX IDX_F52993981E434756 ON `order` (mosquito_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993981E434756');
        $this->addSql('DROP INDEX IDX_F52993981E434756 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP mosquito_id');
    }
}
