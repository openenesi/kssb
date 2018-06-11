<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180610113857 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE account_counter (account_counter_id INT AUTO_INCREMENT NOT NULL, accounts INT NOT NULL, date_initial DATETIME NOT NULL, date_last DATETIME NOT NULL, PRIMARY KEY(account_counter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE deleted_candidate');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL, CHANGE account_no account_no CHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE candidate CHANGE bvn bvn CHAR(11) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE deleted_candidate (user_id INT NOT NULL, candidatebank_id INT DEFAULT NULL, candidatepersonal_id INT DEFAULT NULL, candidateinstitution_id INT DEFAULT NULL, username VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, pwd VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, matric_no VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, mobile_no VARCHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, email VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, app_id INT UNSIGNED DEFAULT NULL, is_active TINYINT(1) NOT NULL, paid TINYINT(1) DEFAULT \'0\' NOT NULL, date_created DATETIME NOT NULL, date_deleted DATETIME NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE account_counter');
        $this->addSql('ALTER TABLE candidate CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE account_no account_no CHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
