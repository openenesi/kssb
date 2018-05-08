<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180427121215 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE candidate (user_id INT AUTO_INCREMENT NOT NULL, candidatebank_id INT DEFAULT NULL, candidatepersonal_id INT DEFAULT NULL, candidateinstitution_id INT DEFAULT NULL, username VARCHAR(30) NOT NULL, pwd VARCHAR(64) NOT NULL, matric_no VARCHAR(30) NOT NULL, bvn VARCHAR(11) NOT NULL, mobile_no VARCHAR(11) NOT NULL, email VARCHAR(30) NOT NULL, app_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C8B28E44F85E0677 (username), UNIQUE INDEX UNIQ_C8B28E44677D5473 (bvn), UNIQUE INDEX UNIQ_C8B28E4460950D04 (mobile_no), UNIQUE INDEX UNIQ_C8B28E44E7927C74 (email), UNIQUE INDEX UNIQ_C8B28E447987212D (app_id), UNIQUE INDEX UNIQ_C8B28E445CC8421E (candidatebank_id), UNIQUE INDEX UNIQ_C8B28E44CCFEE0F (candidatepersonal_id), UNIQUE INDEX UNIQ_C8B28E4491660285 (candidateinstitution_id), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E445CC8421E FOREIGN KEY (candidatebank_id) REFERENCES candidate_bank (candidatebank_id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44CCFEE0F FOREIGN KEY (candidatepersonal_id) REFERENCES candidate_personal (candidatepersonal_id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4491660285 FOREIGN KEY (candidateinstitution_id) REFERENCES candidate_institution (candidateinstitution_id)');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn VARCHAR(11) NOT NULL, CHANGE account_no account_no VARCHAR(10) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE candidate');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn VARCHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE account_no account_no VARCHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
