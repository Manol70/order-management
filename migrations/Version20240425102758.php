<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425102758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mosquito_history (id INT AUTO_INCREMENT NOT NULL, mosquito_id INT DEFAULT NULL, user_id INT DEFAULT NULL, _order_id INT DEFAULT NULL, number_order INT DEFAULT NULL, INDEX IDX_E6B24AFB1E434756 (mosquito_id), INDEX IDX_E6B24AFBA76ED395 (user_id), INDEX IDX_E6B24AFBA35F2858 (_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mosquito_history ADD CONSTRAINT FK_E6B24AFB1E434756 FOREIGN KEY (mosquito_id) REFERENCES mosquito (id)');
        $this->addSql('ALTER TABLE mosquito_history ADD CONSTRAINT FK_E6B24AFBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mosquito_history ADD CONSTRAINT FK_E6B24AFBA35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mosquito_history DROP FOREIGN KEY FK_E6B24AFB1E434756');
        $this->addSql('ALTER TABLE mosquito_history DROP FOREIGN KEY FK_E6B24AFBA76ED395');
        $this->addSql('ALTER TABLE mosquito_history DROP FOREIGN KEY FK_E6B24AFBA35F2858');
        $this->addSql('DROP TABLE mosquito_history');
    }
}
