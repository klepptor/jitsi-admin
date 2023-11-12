<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915133758 extends AbstractMigration
{
    private const TABLE_NAME = 'rooms';
    private const COLUMN_NAME = 'max_user';
    public function getDescription(): string
    {
        return '';
    }




    public function up(Schema $schema): void
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->addColumn(self::COLUMN_NAME,Types::INTEGER)
            ->setDefault(null)
            ->setNotnull(false);
        // this up() migration is auto-generated, please modify it to your needs
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->dropColumn(self::COLUMN_NAME);
        // this down() migration is auto-generated, please modify it to your needs
    }

}
