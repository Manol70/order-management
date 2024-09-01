<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418212807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD type_id INT NOT NULL, ADD customer_id INT DEFAULT NULL, ADD type_montage_id INT NOT NULL, ADD user_id INT NOT NULL, ADD glass_id INT DEFAULT NULL, ADD status_id INT DEFAULT NULL, ADD quadrature DOUBLE PRECISION NOT NULL, ADD for_date DATETIME NOT NULL, ADD price DOUBLE PRECISION NOT NULL, ADD paid DOUBLE PRECISION NOT NULL, ADD scheme VARCHAR(50) DEFAULT NULL, ADD note VARCHAR(255) NOT NULL, ADD status_mail TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F94E55E6 FOREIGN KEY (type_montage_id) REFERENCES type_montage (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986E4A05EA FOREIGN KEY (glass_id) REFERENCES glass (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_F5299398C54C8C93 ON `order` (type_id)');
        $this->addSql('CREATE INDEX IDX_F52993989395C3F3 ON `order` (customer_id)');
        $this->addSql('CREATE INDEX IDX_F5299398F94E55E6 ON `order` (type_montage_id)');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON `order` (user_id)');
        $this->addSql('CREATE INDEX IDX_F52993986E4A05EA ON `order` (glass_id)');
        $this->addSql('CREATE INDEX IDX_F52993986BF700BD ON `order` (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C54C8C93');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F94E55E6');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986E4A05EA');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986BF700BD');
        $this->addSql('DROP INDEX IDX_F5299398C54C8C93 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993989395C3F3 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398F94E55E6 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398A76ED395 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993986E4A05EA ON `order`');
        $this->addSql('DROP INDEX IDX_F52993986BF700BD ON `order`');
        $this->addSql('ALTER TABLE `order` DROP type_id, DROP customer_id, DROP type_montage_id, DROP user_id, DROP glass_id, DROP status_id, DROP quadrature, DROP for_date, DROP price, DROP paid, DROP scheme, DROP note, DROP status_mail');
    }
}
