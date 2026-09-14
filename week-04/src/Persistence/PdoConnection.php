<?php

declare(strict_types=1);

namespace MicroHis\Persistence;

use PDO;

final class PdoConnection
{
    public static function create(string $databasePath): PDO
    {
        $pdo = new PDO('sqlite:' . $databasePath);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    }
}
