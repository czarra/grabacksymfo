<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181230215331 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_2DA17977F85E0677 ON user');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\', DROP username, DROP salt, DROP is_active, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_2da17977e7927c74 TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD username VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, ADD salt VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci, ADD is_active TINYINT(1) NOT NULL, DROP roles, CHANGE email email VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(40) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DA17977F85E0677 ON user (username)');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_2DA17977E7927C74');
    }
}
