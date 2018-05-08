<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180427233146 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn VARCHAR(11) NOT NULL, CHANGE account_no account_no VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE candidate ADD paid TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE bvn bvn VARCHAR(11) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate DROP paid, CHANGE bvn bvn VARCHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn VARCHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE account_no account_no VARCHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
