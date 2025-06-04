<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250530075504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F529939852D06999
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE customer_address (customer_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_1193CB3F9395C3F3 (customer_id), INDEX IDX_1193CB3FF5B7AF75 (address_id), PRIMARY KEY(customer_id, address_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_address ADD CONSTRAINT FK_1193CB3F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_address ADD CONSTRAINT FK_1193CB3FF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BF5B7AF75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_address
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F529939852D06999 ON `order`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` CHANGE user_address_id customer_address_id INT NOT NULL, CHANGE user_name customer_name VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD CONSTRAINT FK_F529939887EABF7 FOREIGN KEY (customer_address_id) REFERENCES customer (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F529939887EABF7 ON `order` (customer_address_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F529939887EABF7
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_address (user_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_5543718BF5B7AF75 (address_id), INDEX IDX_5543718BA76ED395 (user_id), PRIMARY KEY(user_id, address_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address ADD CONSTRAINT FK_5543718BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address ADD CONSTRAINT FK_5543718BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_address DROP FOREIGN KEY FK_1193CB3F9395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_address DROP FOREIGN KEY FK_1193CB3FF5B7AF75
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE customer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE customer_address
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F529939887EABF7 ON `order`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` CHANGE customer_address_id user_address_id INT NOT NULL, CHANGE customer_name user_name VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD CONSTRAINT FK_F529939852D06999 FOREIGN KEY (user_address_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F529939852D06999 ON `order` (user_address_id)
        SQL);
    }
}
