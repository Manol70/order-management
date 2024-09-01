<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422224636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE glass_history ADD _order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE glass_history ADD CONSTRAINT FK_22767D21A35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE glass_history ADD CONSTRAINT FK_22767D21A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE glass_history ADD CONSTRAINT FK_22767D216E4A05EA FOREIGN KEY (glass_id) REFERENCES glass (id)');
        $this->addSql('CREATE INDEX IDX_22767D21A35F2858 ON glass_history (_order_id)');
        $this->addSql('CREATE INDEX IDX_22767D21A76ED395 ON glass_history (user_id)');
        $this->addSql('CREATE INDEX IDX_22767D216E4A05EA ON glass_history (glass_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE glass_history DROP FOREIGN KEY FK_22767D21A35F2858');
        $this->addSql('ALTER TABLE glass_history DROP FOREIGN KEY FK_22767D21A76ED395');
        $this->addSql('ALTER TABLE glass_history DROP FOREIGN KEY FK_22767D216E4A05EA');
        $this->addSql('DROP INDEX IDX_22767D21A35F2858 ON glass_history');
        $this->addSql('DROP INDEX IDX_22767D21A76ED395 ON glass_history');
        $this->addSql('DROP INDEX IDX_22767D216E4A05EA ON glass_history');
        $this->addSql('ALTER TABLE glass_history DROP _order_id');
    }
}
