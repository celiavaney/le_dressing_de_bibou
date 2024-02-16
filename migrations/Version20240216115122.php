<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216115122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, sexe VARCHAR(20) NOT NULL, description VARCHAR(50) DEFAULT NULL, prix_achete INT DEFAULT NULL, prix_vente INT DEFAULT NULL, offert_par VARCHAR(30) DEFAULT NULL, enfants_id INT NOT NULL, categories_id INT NOT NULL, tailles_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BFDD3168A586286C (enfants_id), INDEX IDX_BFDD3168A21214B7 (categories_id), INDEX IDX_BFDD31681AEC613E (tailles_id), INDEX IDX_BFDD3168A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE categories_enfants (categories_id INT NOT NULL, enfants_id INT NOT NULL, INDEX IDX_BE86B63A21214B7 (categories_id), INDEX IDX_BE86B63A586286C (enfants_id), PRIMARY KEY(categories_id, enfants_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE enfants (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(30) NOT NULL, user_id INT NOT NULL, INDEX IDX_23E2BAC3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tailles (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tailles_enfants (tailles_id INT NOT NULL, enfants_id INT NOT NULL, INDEX IDX_78B993441AEC613E (tailles_id), INDEX IDX_78B99344A586286C (enfants_id), PRIMARY KEY(tailles_id, enfants_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A586286C FOREIGN KEY (enfants_id) REFERENCES enfants (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31681AEC613E FOREIGN KEY (tailles_id) REFERENCES tailles (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE categories_enfants ADD CONSTRAINT FK_BE86B63A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories_enfants ADD CONSTRAINT FK_BE86B63A586286C FOREIGN KEY (enfants_id) REFERENCES enfants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enfants ADD CONSTRAINT FK_23E2BAC3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tailles_enfants ADD CONSTRAINT FK_78B993441AEC613E FOREIGN KEY (tailles_id) REFERENCES tailles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tailles_enfants ADD CONSTRAINT FK_78B99344A586286C FOREIGN KEY (enfants_id) REFERENCES enfants (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A586286C');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A21214B7');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31681AEC613E');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A76ED395');
        $this->addSql('ALTER TABLE categories_enfants DROP FOREIGN KEY FK_BE86B63A21214B7');
        $this->addSql('ALTER TABLE categories_enfants DROP FOREIGN KEY FK_BE86B63A586286C');
        $this->addSql('ALTER TABLE enfants DROP FOREIGN KEY FK_23E2BAC3A76ED395');
        $this->addSql('ALTER TABLE tailles_enfants DROP FOREIGN KEY FK_78B993441AEC613E');
        $this->addSql('ALTER TABLE tailles_enfants DROP FOREIGN KEY FK_78B99344A586286C');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_enfants');
        $this->addSql('DROP TABLE enfants');
        $this->addSql('DROP TABLE tailles');
        $this->addSql('DROP TABLE tailles_enfants');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
