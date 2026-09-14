# Evidencia verificable — Actividad SOLID / LSP

## 1. Identificación

- **Estudiante:** Esaú Abimael de la Cruz Mendoza
- **Usuario GitHub:** `00AbiMendoza`
- **Repositorio:** `uml-asii-08-traslados-altas-camas`
- **Módulo:** ASII-08 — Traslados, altas y liberación de camas
- **Actividad:** Aplicación de Liskov Substitution Principle (LSP)
- **Flujo:** Traslado o alta del paciente con liberación consistente de cama
- **Rama de trabajo:** `feature/lsp-traslados-altas-liberacion-camas`
- **Datos utilizados:** exclusivamente ficticios

## 2. Punto de partida

La actividad LSP se desarrolló en una rama independiente a partir de la entrega UML anterior.

Punto de referencia previo:

- **Tag anterior:** `entrega-uml-asii-08-v1.0`
- **Commit base:** `145943a`
- **Rama anterior:** `feature/diagramas-uml-asii-08`

Esto permite conservar intacta la entrega UML previa y separar los cambios correspondientes a la actividad SOLID.

## 3. Commits realizados para LSP

| Commit | Propósito |
|---|---|
| `a99ab4d` | Definir RF, RNF y criterios de aceptación. |
| `afcdfa7` | Documentar el diseño antes/después y la justificación de LSP. |
| `66fcaaf` | Agregar diagramas editables y representaciones PNG del diseño antes/después. |
| `0b554f1` | Ampliar la declaración de uso de inteligencia artificial. |
| `2f553a6` | Agregar la guía específica para defensa oral de LSP. |

Los commits se realizaron con propósitos diferenciados y verificables.

## 4. Artefactos de requisitos y trazabilidad

### `trazabilidad/lsp-rf-rnf-criterios.md`

Contiene:

- requisitos funcionales;
- requisitos no funcionales;
- criterios de aceptación;
- invariantes comunes;
- reglas específicas de traslado;
- reglas específicas de alta;
- criterio global de aceptación para LSP.

### `trazabilidad/lsp-diseno-antes-despues.md`

Contiene:

- descripción del problema;
- diseño antes;
- explicación de la violación de LSP;
- diseño después;
- responsabilidades;
- dependencias;
- comparación antes/después;
- conclusión técnica.

## 5. Artefactos UML editables

Se generaron las siguientes fuentes PlantUML:

- `diagramas/lsp-diseno-antes.puml`
- `diagramas/lsp-diseno-despues.puml`

Estas fuentes son editables y permiten regenerar los diagramas.

También se generaron:

- `diagramas/lsp-diseno-antes.png`
- `diagramas/lsp-diseno-despues.png`

## 6. Validación de PlantUML

El entorno local dispone de:

- OpenJDK 17;
- PlantUML en `C:\Users\esaum\tools\plantuml\plantuml.jar`.

El diseño antes se generó mediante:

    java -jar C:\Users\esaum\tools\plantuml\plantuml.jar -tpng .\diagramas\lsp-diseno-antes.puml

Resultado verificado:

- archivo: `lsp-diseno-antes.png`;
- tamaño observado: 41831 bytes;
- generación sin errores de PlantUML.

El diseño después se generó mediante:

    java -jar C:\Users\esaum\tools\plantuml\plantuml.jar -tpng .\diagramas\lsp-diseno-despues.puml

Resultado verificado:

- archivo: `lsp-diseno-despues.png`;
- tamaño observado: 46441 bytes;
- generación sin errores de PlantUML.

## 7. Evidencia de aplicación de LSP

### Diseño antes

La abstracción `SalidaPaciente` define reglas demasiado específicas:

- cerrar siempre la admisión;
- liberar la cama;
- dejarla directamente disponible.

`TrasladoPaciente` no puede respetar estas expectativas porque:

- mantiene activa la admisión;
- requiere una cama destino;
- ocupa la cama destino;
- deja la cama origen en `limpieza`.

Por ello, el consumidor necesitaría reconocer el subtipo y corregir su comportamiento.

### Diseño después

Se utiliza el contrato:

`OperacionDisposicionPaciente`

Las implementaciones:

- `OperacionTraslado`;
- `OperacionAlta`;

respetan las invariantes comunes sin contradecir el contrato.

El consumidor puede ejecutar cualquiera de ellas mediante la misma abstracción sin necesitar condiciones especiales por subtipo.

## 8. Validaciones realizadas

Durante el trabajo se verificó:

1. que los archivos PlantUML fueran procesados sin errores;
2. que los PNG fueran generados;
3. que los requisitos estuvieran vinculados con criterios de aceptación;
4. que traslado y alta conservaran sus comportamientos específicos;
5. que la cama origen quedara en `limpieza`;
6. que el traslado mantuviera activa la admisión;
7. que el alta cerrara la admisión;
8. que el diseño después permitiera sustituibilidad mediante el contrato común;
9. que se utilizaran únicamente datos ficticios;
10. que cada unidad de trabajo quedara registrada mediante Git.

## 9. Declaración de IA

El archivo:

`DECLARACION_IA.md`

documenta:

- herramienta utilizada;
- propósito;
- consultas relevantes;
- contenido aceptado;
- contenido revisado o modificado;
- validación humana.

La declaración incluye una ampliación específica para esta actividad LSP.

## 10. Defensa oral

El archivo:

`defensa/guia-defensa-lsp.md`

incluye:

- explicación breve de LSP;
- problema del diseño inicial;
- solución aplicada;
- invariantes;
- reglas específicas;
- preguntas probables;
- explicación corta para exposición;
- posible modificación durante la defensa.

## 11. Historial Git observado

El historial verificado durante el desarrollo fue:

    2f553a6 docs(lsp): agregar guia de defensa oral
    0b554f1 docs(lsp): ampliar declaracion de uso de IA
    66fcaaf docs(lsp): agregar diagramas de diseno antes y despues
    afcdfa7 docs(lsp): documentar diseno antes y despues
    a99ab4d docs(lsp): definir RF RNF y criterios de aceptacion
    145943a docs(evidencia): actualizar historial estructura y referencia final

## 12. Evidencia final pendiente de cierre

Al finalizar la entrega se incorporarán en esta sección:

- commit final evaluado;
- tag final de la actividad;
- enlace verificable al commit;
- árbol final de archivos;
- confirmación de publicación de la rama en GitHub.
## 13. Referencia del informe final

- **Commit del informe final:** `b330e05ef6be35f6551a7d10d99308a65197a756`
- **Enlace al commit:** https://github.com/00AbiMendoza/uml-asii-08-traslados-altas-camas/commit/b330e05ef6be35f6551a7d10d99308a65197a756
- **Rama:** `feature/lsp-traslados-altas-liberacion-camas`
- **Etiqueta prevista para el cierre:** `entrega-lsp-asii-08-v1.0`

El commit indicado contiene el informe final en formato DOCX, su fuente de contenido, los datos de portada y el generador específico utilizado para producirlo.

La etiqueta final se aplicará al commit de cierre de la entrega, después de completar esta evidencia y verificar el árbol final del repositorio.