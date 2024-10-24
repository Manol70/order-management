<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023142825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Създаване на индекс на полето balance
    $this->addSql('CREATE INDEX idx_balance ON `order` (balance)');

    }

    public function down(Schema $schema): void
    {
        // Премахване на индекса
    $this->addSql('DROP INDEX idx_balance ON `order`');

    }
}
