<?php

declare(strict_types=1);

namespace MicroHis\Domain\Repositories;

use MicroHis\Domain\Entities\Bed;

interface BedRepository
{
    public function findById(int $id): ?Bed;

    public function save(Bed $bed): void;
}
