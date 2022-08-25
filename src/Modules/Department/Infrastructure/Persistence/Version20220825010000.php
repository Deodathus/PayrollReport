<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220825010000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("
            create table departments (
                id BINARY(36) not null unique,
                name varchar(255) not null,
                salary_bonus int not null default 0,
                salary_bonus_type enum('percentage', 'fixed_amount'),
                primary key (id)
            )
        ");
    }
}
