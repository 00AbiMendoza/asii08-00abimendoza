# Semana 5 — Cliente-servidor, API REST, microservicios e integración

**Tema:** Cliente-servidor, API REST, microservicios e integración.

**Tarea:** Evolucionar el mismo «Micro-HIS Traslados, altas y liberación de camas» (semanas 3-4) hacia un diseño cliente-servidor y evaluar una frontera de microservicio para «traslado o alta del paciente con liberación consistente de cama». Especificar contrato API, propiedad de datos, comunicación, seguridad, resiliencia, observabilidad y consistencia; incluir diagramas y una migración razonada.

**Entregable:** Contrato API y plan de issue, rama, worktree y PR.

## Contenido

- `semana-05-cliente-servidor-microservicios.md` — documento completo
- `migracion-cliente-servidor.puml` / `.png` — diagrama de la migración Micro-HIS (monolito local) → backend Laravel (cliente-servidor)
- `frontera-microservicio.puml` / `.png` — diagrama de la frontera del módulo frente a los demás módulos del sistema

Esta semana documenta una migración **ya implementada**: el Micro-HIS de las semanas 3-4 fue el prototipo; el backend Laravel del repositorio grupal es el diseño cliente-servidor evolucionado. El documento explica qué cambió, qué se mantuvo, y evalúa (con justificación medible) por qué el módulo no se separa como servicio desplegado independiente por ahora.

Contenido replicado desde el repositorio grupal (PR #67, `docs/modules/asii-08/`).
