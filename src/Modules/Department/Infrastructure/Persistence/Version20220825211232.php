<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220825211232 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("
            create table employees (
                id BINARY(36) not null unique,
                department_id binary(36) not null,
                first_name varchar(255) not null,
                last_name varchar(255) not null,
                hired_at datetime not null default now(),
                salary int not null default 0,
                primary key (id),
                foreign key (department_id) references departments(id)
            )
        ");
    }
}
