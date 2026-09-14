<?php

declare(strict_types=1);

namespace MicroHis\Persistence;

use MicroHis\Domain\Entities\Bed;
use MicroHis\Domain\Repositories\BedRepository;
use PDO;

final class PdoBedRepository implements BedRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?Bed
    {
        $statement = $this->pdo->prepare(
            'SELECT id, code, status
             FROM beds
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
        ]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return new Bed(
            (int) $row['id'],
            (string) $row['code'],
            (string) $row['status']
        );
    }

    public function save(Bed $bed): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE beds
             SET code = :code,
                 status = :status
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $bed->id(),
            'code' => $bed->code(),
            'status' => $bed->status(),
        ]);
    }
}
