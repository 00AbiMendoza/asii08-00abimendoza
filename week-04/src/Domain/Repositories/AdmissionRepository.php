<?php

declare(strict_types=1);

namespace MicroHis\Domain\Repositories;

use MicroHis\Domain\Entities\Admission;

interface AdmissionRepository
{
    public function findById(int $id): ?Admission;

    public function save(Admission $admission): void;
}
