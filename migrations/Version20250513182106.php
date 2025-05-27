<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513182106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE product_attributes (id INT AUTO_INCREMENT NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, width DOUBLE PRECISION DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_attributes_product (product_attributes_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_AB16671CEF0C43FE (product_attributes_id), INDEX IDX_AB16671C4584665A (product_id), PRIMARY KEY(product_attributes_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product ADD CONSTRAINT FK_AB16671CEF0C43FE FOREIGN KEY (product_attributes_id) REFERENCES product_attributes (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product ADD CONSTRAINT FK_AB16671C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product DROP FOREIGN KEY FK_AB16671CEF0C43FE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product DROP FOREIGN KEY FK_AB16671C4584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_attributes
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_attributes_product
        SQL);
    }
}
