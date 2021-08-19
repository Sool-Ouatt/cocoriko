<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210729170416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (telephone BIGINT NOT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(telephone)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id VARCHAR(21) NOT NULL, date_commande DATE NOT NULL, valeur DOUBLE PRECISION NOT NULL, adresse_livraison VARCHAR(120) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status VARCHAR(11) NOT NULL, teleploneClient BIGINT NOT NULL, INDEX IDX_6EEAA67DACD3ACA2 (teleploneClient), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contenu (id INT AUTO_INCREMENT NOT NULL, qunatite INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, idProduit INT NOT NULL, idCommande VARCHAR(21) NOT NULL, INDEX IDX_89C2003F391C87D5 (idProduit), INDEX IDX_89C2003F3D498C26 (idCommande), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id_entreprise VARCHAR(75) NOT NULL, ville VARCHAR(30) NOT NULL, quartier VARCHAR(40) NOT NULL, rue VARCHAR(25) DEFAULT NULL, porte VARCHAR(3) DEFAULT NULL, telephone_entreprise BIGINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, telephoneResponsable BIGINT NOT NULL, INDEX IDX_D19FA60D4B0C9DA (telephoneResponsable), PRIMARY KEY(id_entreprise)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(100) NOT NULL, poids DOUBLE PRECISION DEFAULT NULL, quantite_stock INT NOT NULL, prix_achat DOUBLE PRECISION NOT NULL, prix_vente DOUBLE PRECISION NOT NULL, taux_reduction INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DACD3ACA2 FOREIGN KEY (teleploneClient) REFERENCES client (telephone)');
        $this->addSql('ALTER TABLE contenu ADD CONSTRAINT FK_89C2003F391C87D5 FOREIGN KEY (idProduit) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE contenu ADD CONSTRAINT FK_89C2003F3D498C26 FOREIGN KEY (idCommande) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60D4B0C9DA FOREIGN KEY (telephoneResponsable) REFERENCES client (telephone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DACD3ACA2');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60D4B0C9DA');
        $this->addSql('ALTER TABLE contenu DROP FOREIGN KEY FK_89C2003F3D498C26');
        $this->addSql('ALTER TABLE contenu DROP FOREIGN KEY FK_89C2003F391C87D5');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE contenu');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE produit');
    }
}
