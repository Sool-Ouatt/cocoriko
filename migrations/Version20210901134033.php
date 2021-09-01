<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210901134033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD telephoneClient BIGINT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A5380CD8 FOREIGN KEY (telephoneClient) REFERENCES client (telephone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A5380CD8 ON user (telephoneClient)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A5380CD8');
        $this->addSql('DROP INDEX UNIQ_8D93D649A5380CD8 ON user');
        $this->addSql('ALTER TABLE user DROP telephoneClient');
    }
}
