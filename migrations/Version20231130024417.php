<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130024417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ADD type_montage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DF94E55E6 FOREIGN KEY (type_montage_id) REFERENCES type_montage (id)');
        $this->addSql('CREATE INDEX IDX_6D28840DF94E55E6 ON payment (type_montage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DF94E55E6');
        $this->addSql('DROP INDEX IDX_6D28840DF94E55E6 ON payment');
        $this->addSql('ALTER TABLE payment DROP type_montage_id');
    }
}
