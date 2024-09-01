<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130213513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ADD _order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_6D28840DA35F2858 ON payment (_order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA35F2858');
        $this->addSql('DROP INDEX IDX_6D28840DA35F2858 ON payment');
        $this->addSql('ALTER TABLE payment DROP _order_id');
    }
}
