<?php
declare(strict_types=1);

namespace PayrollReport\Modules\Department\Application\Department\GetById;

use PayrollReport\Modules\Department\Application\Department\Department;
use PayrollReport\Modules\Department\Application\Department\DepartmentReadModel;
use PayrollReport\Shared\Application\NotFoundException;
use PayrollReport\Shared\Application\Query\QueryHandler;
use Psr\Log\LoggerInterface;

final class GetDepartmentByIdHandler implements QueryHandler
{
    public function __construct(
        private readonly DepartmentReadModel $departmentReadModel,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(GetDepartmentByIdQuery $getDepartmentByIdCommand): Department
    {
        try {
            return $this->departmentReadModel->fetchById($getDepartmentByIdCommand->id);
        } catch (NotFoundException $exception) {
            $this->logger->error(
                sprintf(
                    '[%s] | Exception message: [%s]',
                    self::class,
                    $exception->getMessage()
                )
            );

            throw $exception;
        }
    }
}
