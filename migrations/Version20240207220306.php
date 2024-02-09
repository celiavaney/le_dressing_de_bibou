<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207220306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE enfants (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tailles (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE articles ADD offert_par VARCHAR(30) DEFAULT NULL, ADD enfants_id INT NOT NULL, ADD categories_id INT NOT NULL, ADD tailles_id INT NOT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A586286C FOREIGN KEY (enfants_id) REFERENCES enfants (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31681AEC613E FOREIGN KEY (tailles_id) REFERENCES tailles (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168A586286C ON articles (enfants_id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168A21214B7 ON articles (categories_id)');
        $this->addSql('CREATE INDEX IDX_BFDD31681AEC613E ON articles (tailles_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE enfants');
        $this->addSql('DROP TABLE tailles');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A586286C');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A21214B7');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31681AEC613E');
        $this->addSql('DROP INDEX IDX_BFDD3168A586286C ON articles');
        $this->addSql('DROP INDEX IDX_BFDD3168A21214B7 ON articles');
        $this->addSql('DROP INDEX IDX_BFDD31681AEC613E ON articles');
        $this->addSql('ALTER TABLE articles DROP offert_par, DROP enfants_id, DROP categories_id, DROP tailles_id');
    }
}
