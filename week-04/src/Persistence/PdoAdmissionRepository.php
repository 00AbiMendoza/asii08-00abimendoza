<?php

declare(strict_types=1);

namespace MicroHis\Persistence;

use MicroHis\Domain\Entities\Admission;
use MicroHis\Domain\Repositories\AdmissionRepository;
use PDO;

final class PdoAdmissionRepository implements AdmissionRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?Admission
    {
        $statement = $this->pdo->prepare(
            'SELECT id, patient_code, bed_id, status
             FROM admissions
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
        ]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return new Admission(
            (int) $row['id'],
            (string) $row['patient_code'],
            (int) $row['bed_id'],
            (string) $row['status']
        );
    }

    public function save(Admission $admission): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE admissions
             SET patient_code = :patient_code,
                 bed_id = :bed_id,
                 status = :status
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $admission->id(),
            'patient_code' => $admission->patientCode(),
            'bed_id' => $admission->bedId(),
            'status' => $admission->status(),
        ]);
    }
}
