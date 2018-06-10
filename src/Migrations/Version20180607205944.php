<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180607205944 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL, CHANGE account_no account_no CHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE candidate ADD trxn_ref VARCHAR(30) DEFAULT NULL, ADD date_paid DATETIME DEFAULT NULL, ADD date_completed DATETIME DEFAULT NULL, CHANGE bvn bvn CHAR(11) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8B28E448FFC3FC4 ON candidate (trxn_ref)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_C8B28E448FFC3FC4 ON candidate');
        $this->addSql('ALTER TABLE candidate DROP trxn_ref, DROP date_paid, DROP date_completed, CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE account_no account_no CHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
