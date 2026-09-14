# Semana 3 — Diseño arquitectónico, vistas y patrones

**Tema:** Diseño arquitectónico, vistas y patrones.

**Tarea:** Iniciar «Micro-HIS Traslados, altas y liberación de camas» en PHP 8.2+ vanilla para ejecutar «traslado o alta del paciente con liberación consistente de cama». Separar Presentation, Application, Domain y Persistence; usar PDO con sentencias preparadas, sin framework, y probar al menos camino feliz, regla de dominio y error de persistencia mediante dobles o una base de prueba.

**Entregable:** Vista arquitectónica C4/UML del módulo y dependencias.

## Contenido

- `diagramas/arquitectura-micro-his.png` / `.puml` — vista de las 4 capas y sus dependencias
- `diagramas/flujo-traslado-alta.png` / `.puml` — flujo del proceso de traslado y alta

La implementación completa de esta arquitectura (código PHP funcional, capa por capa, incluyendo el patrón Repository) está en [`week-04`](../week-04/) — ambas semanas comparten el mismo Micro-HIS, construido de forma continua.

Contenido replicado desde el repositorio grupal (PR #67) y desde el repositorio personal [`micro-his-asii-08-traslados-altas-camas`](https://github.com/00AbiMendoza/micro-his-asii-08-traslados-altas-camas), donde se desarrolló originalmente.
