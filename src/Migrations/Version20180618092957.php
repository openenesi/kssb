<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180618092957 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate_institution CHANGE matric_no matric_no VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL, CHANGE account_no account_no CHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE candidate_personal CHANGE email email VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE candidate CHANGE username username VARCHAR(100) NOT NULL, CHANGE matric_no matric_no VARCHAR(50) NOT NULL, CHANGE bvn bvn CHAR(11) NOT NULL, CHANGE email email VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate CHANGE username username VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE matric_no matric_no VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE account_no account_no CHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_institution CHANGE matric_no matric_no VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_personal CHANGE email email VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
