<?php

declare(strict_types=1);

namespace MicroHis\Persistence;

use MicroHis\Application\Contracts\TransactionManager;
use PDO;

final class PdoTransactionManager implements TransactionManager
{
    public function __construct(private PDO $pdo)
    {
    }

    public function begin(): void
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
