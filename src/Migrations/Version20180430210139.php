<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180430210139 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate_institution CHANGE institution_addr institution_addr VARCHAR(60) NOT NULL, CHANGE faculty faculty VARCHAR(60) NOT NULL, CHANGE department department VARCHAR(60) NOT NULL, CHANGE course_of_study course_of_study VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL, CHANGE account_no account_no CHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE candidate_personal CHANGE home_addr home_addr VARCHAR(100) NOT NULL, CHANGE nok_addr nok_addr VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE candidate CHANGE bvn bvn CHAR(11) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE account_no account_no CHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_institution CHANGE institution_addr institution_addr VARCHAR(200) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE faculty faculty VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE department department VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE course_of_study course_of_study VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_personal CHANGE home_addr home_addr VARCHAR(300) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE nok_addr nok_addr VARCHAR(300) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
