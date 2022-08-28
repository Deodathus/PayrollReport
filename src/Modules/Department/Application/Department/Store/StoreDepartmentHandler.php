<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department\Store;

use PayrollReport\Modules\Department\Application\Department\ApplicationException;
use PayrollReport\Modules\Department\Domain\Department\BonusAmount;
use PayrollReport\Modules\Department\Domain\Department\BonusAmountException;
use PayrollReport\Modules\Department\Domain\Department\Department;
use PayrollReport\Modules\Department\Domain\Department\DepartmentRepository;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonus;
use PayrollReport\Modules\Department\Domain\Department\DepartmentSalaryBonusType;
use PayrollReport\Shared\Application\Command\CommandHandler;
use Psr\Log\LoggerInterface;

final class StoreDepartmentHandler implements CommandHandler
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepository,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @throws ApplicationException
     */
    public function __invoke(StoreDepartmentCommand $storeDepartmentCommand): void
    {
        try {
            $department = Department::create(
                $storeDepartmentCommand->name,
                new DepartmentSalaryBonus(
                    DepartmentSalaryBonusType::from($storeDepartmentCommand->salaryBonusType),
                    new BonusAmount((int)($storeDepartmentCommand->salaryBonus * 100))
                )
            );

            $this->departmentRepository->store($department);
        } catch (BonusAmountException $e) {
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
