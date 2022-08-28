<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Employee\Store;

use PayrollReport\Modules\Department\Application\Employee\ApplicationException;
use PayrollReport\Modules\Department\Domain\Department\DepartmentId;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Employee\Employee;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeExperience;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeName;
use PayrollReport\Modules\Department\Domain\Employee\EmployeeRepository;
use PayrollReport\Modules\Department\Domain\Employee\Salary;
use PayrollReport\Modules\Department\Domain\Employee\SalaryException;
use PayrollReport\Shared\Application\Command\CommandHandler;
use PayrollReport\Shared\Application\NotFoundException;
use Psr\Log\LoggerInterface;

final class StoreEmployeeHandler implements CommandHandler
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly DepartmentRepository $departmentRepository,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @throws NotFoundException
     * @throws ApplicationException
     */
    public function __invoke(StoreEmployeeCommand $storeEmployeeCommand): void
    {
        try {
            $departmentExists = $this->departmentRepository->existsWithId($storeEmployeeCommand->departmentId);

            if (!$departmentExists) {
                throw NotFoundException::notFoundById($storeEmployeeCommand->departmentId);
            }

            $employee = Employee::create(
                DepartmentId::fromString($storeEmployeeCommand->departmentId),
                new EmployeeName($storeEmployeeCommand->firstName, $storeEmployeeCommand->lastName),
                new EmployeeExperience($storeEmployeeCommand->hiredAt),
                new Salary((int) ($storeEmployeeCommand->salary * 100))
            );

            $this->employeeRepository->store($employee);
        } catch (SalaryException $e) {
            $this->logger->error(
                sprintf(
                    '[%s] | Exception message: [%s]',
                    self::class,
                    $e->getMessage()
                )
            );

            throw ApplicationException::fromDomainException($e);
        }
    }
}
