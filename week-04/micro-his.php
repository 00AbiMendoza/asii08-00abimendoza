<?php

declare(strict_types=1);

use MicroHis\Application\UseCases\DischargePatient;
use MicroHis\Application\UseCases\TransferPatient;
use MicroHis\Persistence\PdoAdmissionRepository;
use MicroHis\Persistence\PdoBedRepository;
use MicroHis\Persistence\PdoConnection;
use MicroHis\Persistence\PdoTransactionManager;
use MicroHis\Presentation\ConsoleApplication;

require __DIR__ . '/autoload.php';

$config = require __DIR__ . '/config/app.php';

if ($config['db_driver'] !== 'sqlite') {
    fwrite(STDERR, "ERROR: Este ejercicio esta configurado para SQLite." . PHP_EOL);
    exit(1);
}

$pdo = PdoConnection::create($config['db_path']);

$bedRepository = new PdoBedRepository($pdo);
$admissionRepository = new PdoAdmissionRepository($pdo);
$transactionManager = new PdoTransactionManager($pdo);

$transferPatient = new TransferPatient(
    $admissionRepository,
    $bedRepository,
    $transactionManager
);

$dischargePatient = new DischargePatient(
    $admissionRepository,
    $bedRepository,
    $transactionManager
);

$application = new ConsoleApplication(
    $transferPatient,
    $dischargePatient
);

exit($application->run($argv));
