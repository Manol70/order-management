<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115114914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD type_montage_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F94E55E6 FOREIGN KEY (type_montage_id) REFERENCES type_montage (id)');
        $this->addSql('CREATE INDEX IDX_F5299398F94E55E6 ON `order` (type_montage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F94E55E6');
        $this->addSql('DROP INDEX IDX_F5299398F94E55E6 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP type_montage_id');
    }
}
