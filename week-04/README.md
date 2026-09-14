# Semana 4 — Arquitectura y patrón Repository

**Tema:** Arquitectura por capas y patrón Repository, aplicados sobre el Micro-HIS iniciado en la semana 3.

## Contenido

Implementación completa en PHP 8.2+ vanilla (sin framework), separada en 4 capas:

- `src/Presentation/` — `ConsoleApplication.php`, punto de entrada por consola
- `src/Application/` — casos de uso (`TransferPatient`, `DischargePatient`) y el contrato `TransactionManager`
- `src/Domain/` — entidades (`Admission`, `Bed`), excepciones de dominio, y las **interfaces de Repository** (`AdmissionRepository`, `BedRepository`) — el dominio depende solo de estos contratos, nunca de la implementación concreta
- `src/Persistence/` — implementaciones del patrón Repository con PDO y sentencias preparadas (`PdoAdmissionRepository`, `PdoBedRepository`), conexión y manejador de transacciones
- `database/` — esquema y datos semilla
- `tests/run.php` — pruebas de camino feliz, regla de dominio y error de persistencia
- `evidencia/` — árbol de archivos, ejecución real del traslado, log de Git, resultados de pruebas
- `docs/InformeMicroHIS.docx` / `.pdf` — informe final
- `GUIA_DEFENSA.md` — guía para la exposición oral

El patrón Repository es lo que permite que `TransferPatient`/`DischargePatient` (Application) trabajen únicamente contra `AdmissionRepository`/`BedRepository` (interfaces en Domain), sin conocer que por debajo se usa PDO/SQLite — la misma separación que ya se aplica en el backend Laravel del repositorio grupal, aquí demostrada en PHP vanilla.

Contenido replicado desde el repositorio grupal (PR #67) y desde el repositorio personal [`micro-his-asii-08-traslados-altas-camas`](https://github.com/00AbiMendoza/micro-his-asii-08-traslados-altas-camas), donde se desarrolló originalmente.
