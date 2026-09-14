# Semana 7 — Diseño de componentes y refactorización

**Tema:** Diseño de componentes y refactorización.

**Tarea:** Diseñar los componentes backend y frontend propios de Traslados, altas y liberación de camas para «traslado o alta del paciente con liberación consistente de cama». Refactorizar conceptualmente el punto con mayor acoplamiento entre traslados, altas, ocupación y liberación atómica de camas, separando UI, aplicación, dominio y persistencia sin ampliar el módulo.

**Entregable:** Diagrama de componentes, contratos de entrada/salida y comparación antes/después de un refactor concreto con justificación.

## Contenido

- `semana-07-componentes-refactorizacion.md` — documento completo
- `componentes-antes.puml` / `.png` — diagrama de componentes sin Repository Pattern (Alternativa B, rechazada)
- `componentes-despues.puml` / `.png` — diagrama de componentes con Repository Pattern (implementado)

El refactor documentado es el que ya se decidió en `ADR-001-arquitectura.md` del repositorio grupal: `TransferPatientService`/`DischargePatientService` pasaron de depender de Eloquent directamente a depender únicamente de 4 interfaces (`AdmissionRepositoryInterface`, `BedRepositoryInterface`, `BedTransferRepositoryInterface`, `TransactionManagerInterface`).

Contenido replicado desde el repositorio grupal (PR #67, `docs/modules/asii-08/`).
