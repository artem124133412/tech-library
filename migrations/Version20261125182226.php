<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261125182226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE books (
                id INT AUTO_INCREMENT NOT NULL,
                isbn VARCHAR(25) NOT NULL,
                title VARCHAR(180) NOT NULL,
                author VARCHAR(120) NOT NULL,
                published_year INT NOT NULL,
                status VARCHAR(20) NOT NULL,
                summary LONGTEXT DEFAULT NULL,
                topic_id INT NOT NULL,
                UNIQUE INDEX UNIQ_4A1B2A92CC1CF4E6 (isbn),
                INDEX IDX_4A1B2A921F55203D (topic_id),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE topics (
                id INT AUTO_INCREMENT NOT NULL,
                slug VARCHAR(40) NOT NULL,
                name VARCHAR(100) NOT NULL,
                UNIQUE INDEX UNIQ_91F64639989D9B62 (slug),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL,
                body LONGTEXT NOT NULL,
                headers LONGTEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL,
                available_at DATETIME NOT NULL,
                delivered_at DATETIME DEFAULT NULL,
                INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (
                    queue_name, available_at, delivered_at,
                    id
                ),
                PRIMARY KEY (id)
            )
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
                books
            ADD
                CONSTRAINT FK_4A1B2A921F55203D FOREIGN KEY (topic_id) REFERENCES topics (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP FOREIGN KEY FK_4A1B2A921F55203D');
        $this->addSql('DROP TABLE books');
        $this->addSql('DROP TABLE topics');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
