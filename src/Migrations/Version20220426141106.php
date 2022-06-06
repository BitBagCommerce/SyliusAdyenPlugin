<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426141106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bitbag_adyen_log (id INT AUTO_INCREMENT NOT NULL, level INT NOT NULL, error_code INT NOT NULL, message VARCHAR(1000) NOT NULL, date_time DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_adyen_reference (id INT AUTO_INCREMENT NOT NULL, refund_payment_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, psp_reference VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7FD033A818C3BB89 (psp_reference), UNIQUE INDEX UNIQ_7FD033A8E739D017 (refund_payment_id), INDEX IDX_7FD033A84C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_adyen_token (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, payment_method_id INT DEFAULT NULL, identifier VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_681C2E9A772E836A (identifier), INDEX IDX_681C2E9A9395C3F3 (customer_id), INDEX IDX_681C2E9A5AA1164F (payment_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bitbag_adyen_reference ADD CONSTRAINT FK_7FD033A8E739D017 FOREIGN KEY (refund_payment_id) REFERENCES sylius_refund_refund_payment (id)');
        $this->addSql('ALTER TABLE bitbag_adyen_reference ADD CONSTRAINT FK_7FD033A84C3A3BB FOREIGN KEY (payment_id) REFERENCES sylius_payment (id)');
        $this->addSql('ALTER TABLE bitbag_adyen_token ADD CONSTRAINT FK_681C2E9A9395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('ALTER TABLE bitbag_adyen_token ADD CONSTRAINT FK_681C2E9A5AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bitbag_adyen_log');
        $this->addSql('DROP TABLE bitbag_adyen_reference');
        $this->addSql('DROP TABLE bitbag_adyen_token');
    }
}
