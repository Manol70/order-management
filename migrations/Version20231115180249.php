<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115180249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD glass_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986E4A05EA FOREIGN KEY (glass_id) REFERENCES glass (id)');
        $this->addSql('CREATE INDEX IDX_F52993986E4A05EA ON `order` (glass_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986E4A05EA');
        $this->addSql('DROP INDEX IDX_F52993986E4A05EA ON `order`');
        $this->addSql('ALTER TABLE `order` DROP glass_id');
    }
}
