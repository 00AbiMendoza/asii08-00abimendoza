# Semana 2 — Principio SOLID: Liskov Substitution Principle (LSP)

**Tema:** Proceso y modelo de diseño; principios SOLID.

**Tarea:** Aplicar LSP al diseño del flujo «traslado o alta del paciente con liberación consistente de cama». Definir RF/RNF y criterios de aceptación; mostrar el diseño antes/después, justificar responsabilidades y dependencias, y aportar evidencia verificable.

**Entregable:** RF/RNF, criterios de aceptación y ejemplo SOLID.

## Contenido

- `diagramas/` — diseño antes (`SalidaPaciente`) y después (`OperacionDisposicionPaciente`)
- `documento/InformeLSP.docx` / `.pdf` — informe final
- `trazabilidad/lsp-diseno-antes-despues.md` — comparación detallada antes/después
- `trazabilidad/lsp-rf-rnf-criterios.md` — requisitos y criterios de aceptación
- `defensa/guia-defensa-lsp.md` — guía para la exposición oral
- `evidencia/evidencia-lsp.md` — evidencia verificable de la aplicación del principio

El rediseño reemplaza la clase base rígida `SalidaPaciente` por el contrato mínimo común `OperacionDisposicionPaciente`, del cual `OperacionTraslado` y `OperacionAlta` son implementaciones sustituibles sin contradecir el contrato — la evidencia central de LSP aplicado en el módulo.

Contenido replicado desde el repositorio grupal (PR #67) y desde el repositorio personal [`uml-asii-08-traslados-altas-camas`](https://github.com/00AbiMendoza/uml-asii-08-traslados-altas-camas), donde se desarrolló originalmente.
