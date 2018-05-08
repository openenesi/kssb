<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180505203819 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction_log ADD reference VARCHAR(50) NOT NULL, ADD amount NUMERIC(7, 2) UNSIGNED NOT NULL, ADD currency VARCHAR(10) NOT NULL, ADD status VARCHAR(20) NOT NULL, ADD gateway_response VARCHAR(100) NOT NULL, ADD message VARCHAR(100) NOT NULL, ADD domain VARCHAR(100) NOT NULL, ADD channel VARCHAR(4) NOT NULL, ADD ip_address VARCHAR(20) NOT NULL, ADD card_type VARCHAR(20) NOT NULL, ADD last4 VARCHAR(4) NOT NULL, ADD bank VARCHAR(40) NOT NULL, ADD country_code VARCHAR(10) NOT NULL, ADD attempts INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_747BDD0CAEA34913 ON transaction_log (reference)');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL, CHANGE account_no account_no CHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE candidate CHANGE bvn bvn CHAR(11) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE candidate CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE candidate_bank CHANGE bvn bvn CHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE account_no account_no CHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_747BDD0CAEA34913 ON transaction_log');
        $this->addSql('ALTER TABLE transaction_log DROP reference, DROP amount, DROP currency, DROP status, DROP gateway_response, DROP message, DROP domain, DROP channel, DROP ip_address, DROP card_type, DROP last4, DROP bank, DROP country_code, DROP attempts');
    }
}
