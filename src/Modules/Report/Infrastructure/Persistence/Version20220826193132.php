<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220826193132 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("
            create table reports (
                id BINARY(36) not null unique,
                generated_at datetime not null default now(),
                primary key (id)
            )
        ");
    }
}
