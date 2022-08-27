<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Report\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220826193212 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("
            create table reports_positions (
                id BINARY(36) not null unique,
                report_id BINARY(36) not null,
                employee_first_name varchar(255) not null,
                employee_last_name varchar(255) not null,
                department_name varchar(255) not null,
                base_salary int not null,
                salary_bonus int not null,
                salary_bonus_type enum('percentage', 'fixed_amount'),
                total_salary int not null,
                primary key (id),
                foreign key (report_id) references reports(id)
            )
        ");
    }
}
