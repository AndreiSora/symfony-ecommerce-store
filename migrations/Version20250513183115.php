<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513183115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD special_price DOUBLE PRECISION DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product DROP FOREIGN KEY FK_AB16671C4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product DROP FOREIGN KEY FK_AB16671CEF0C43FE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_ab16671cef0c43fe ON product_attributes_product
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AD938BEB60425CFB ON product_attributes_product (product_attributes_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_ab16671c4584665a ON product_attributes_product
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AD938BEB4584665A ON product_attributes_product (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product ADD CONSTRAINT FK_AB16671C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product ADD CONSTRAINT FK_AB16671CEF0C43FE FOREIGN KEY (product_attributes_id) REFERENCES product_attributes (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP special_price
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product DROP FOREIGN KEY FK_AD938BEB60425CFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product DROP FOREIGN KEY FK_AD938BEB4584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_ad938beb4584665a ON product_attributes_product
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AB16671C4584665A ON product_attributes_product (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_ad938beb60425cfb ON product_attributes_product
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AB16671CEF0C43FE ON product_attributes_product (product_attributes_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product ADD CONSTRAINT FK_AD938BEB60425CFB FOREIGN KEY (product_attributes_id) REFERENCES product_attributes (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_attributes_product ADD CONSTRAINT FK_AD938BEB4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
    }
}
