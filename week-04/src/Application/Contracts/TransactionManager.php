<?php

declare(strict_types=1);

namespace MicroHis\Application\Contracts;

interface TransactionManager
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;
}
