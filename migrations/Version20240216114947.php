<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216114947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories_enfants (categories_id INT NOT NULL, enfants_id INT NOT NULL, INDEX IDX_BE86B63A21214B7 (categories_id), INDEX IDX_BE86B63A586286C (enfants_id), PRIMARY KEY(categories_id, enfants_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tailles_enfants (tailles_id INT NOT NULL, enfants_id INT NOT NULL, INDEX IDX_78B993441AEC613E (tailles_id), INDEX IDX_78B99344A586286C (enfants_id), PRIMARY KEY(tailles_id, enfants_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE categories_enfants ADD CONSTRAINT FK_BE86B63A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories_enfants ADD CONSTRAINT FK_BE86B63A586286C FOREIGN KEY (enfants_id) REFERENCES enfants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tailles_enfants ADD CONSTRAINT FK_78B993441AEC613E FOREIGN KEY (tailles_id) REFERENCES tailles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tailles_enfants ADD CONSTRAINT FK_78B99344A586286C FOREIGN KEY (enfants_id) REFERENCES enfants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168A76ED395 ON articles (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories_enfants DROP FOREIGN KEY FK_BE86B63A21214B7');
        $this->addSql('ALTER TABLE categories_enfants DROP FOREIGN KEY FK_BE86B63A586286C');
        $this->addSql('ALTER TABLE tailles_enfants DROP FOREIGN KEY FK_78B993441AEC613E');
        $this->addSql('ALTER TABLE tailles_enfants DROP FOREIGN KEY FK_78B99344A586286C');
        $this->addSql('DROP TABLE categories_enfants');
        $this->addSql('DROP TABLE tailles_enfants');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A76ED395');
        $this->addSql('DROP INDEX IDX_BFDD3168A76ED395 ON articles');
    }
}
