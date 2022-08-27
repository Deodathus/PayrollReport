<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Domain\Department;

interface DepartmentRepository
{
    public function store(Department $department): void;

    public function existsWithId(string $id): bool;

    public function fetchSalaryBonus(string $id): DepartmentSalaryBonus;

    /**
     * @return string[]
     */
    public function fetchNames(): array;
}
