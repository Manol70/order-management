<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424104746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE status_history ADD _order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE status_history ADD CONSTRAINT FK_2F6A07CEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE status_history ADD CONSTRAINT FK_2F6A07CEA35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_2F6A07CEA76ED395 ON status_history (user_id)');
        $this->addSql('CREATE INDEX IDX_2F6A07CEA35F2858 ON status_history (_order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE status_history DROP FOREIGN KEY FK_2F6A07CEA76ED395');
        $this->addSql('ALTER TABLE status_history DROP FOREIGN KEY FK_2F6A07CEA35F2858');
        $this->addSql('DROP INDEX IDX_2F6A07CEA76ED395 ON status_history');
        $this->addSql('DROP INDEX IDX_2F6A07CEA35F2858 ON status_history');
        $this->addSql('ALTER TABLE status_history DROP _order_id');
    }
}
