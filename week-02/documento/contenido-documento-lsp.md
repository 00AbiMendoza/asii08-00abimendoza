# Informe individual — Aplicación de LSP

## 1. Introducción

El presente trabajo desarrolla una mejora de diseño para el módulo ASII-08 — Traslados, altas y liberación de camas. El análisis se concentra en el flujo de traslado interno o alta hospitalaria de un paciente y en la liberación consistente de la cama de origen.

La actividad aplica el principio de sustitución de Liskov (LSP) para evaluar si las operaciones de traslado y alta pueden utilizarse mediante una abstracción común sin producir comportamientos incompatibles. Para demostrarlo se definen requisitos funcionales y no funcionales, criterios de aceptación, invariantes compartidas, un diseño previo con problemas de sustituibilidad y un diseño posterior que corrige dichas deficiencias.

La evidencia se conserva mediante fuentes editables PlantUML, documentos de trazabilidad, historial Git, declaración de uso de inteligencia artificial y una guía de preparación para la defensa oral. Todos los ejemplos académicos utilizan datos exclusivamente ficticios.

## 2. Alcance y asignación individual

La asignación individual corresponde al módulo:

**ASII-08 — Traslados, altas y liberación de camas.**

El flujo evaluado es:

**Traslado o alta del paciente con liberación consistente de cama.**

El principio SOLID asignado es:

**Liskov Substitution Principle (LSP).**

El alcance comprende:

- definición de RF y RNF;
- formulación de criterios de aceptación;
- identificación de invariantes comunes;
- representación del diseño antes de aplicar LSP;
- representación del diseño después de aplicar LSP;
- justificación de responsabilidades;
- identificación de dependencias;
- evidencia técnica verificable;
- evidencia Git;
- declaración transparente del uso de IA;
- preparación para defensa oral.

No forma parte del alcance implementar un sistema hospitalario productivo ni utilizar datos clínicos reales.

## 3. Requisitos y criterios de aceptación

Los requisitos fueron organizados específicamente para el flujo de traslado y alta.

Los requisitos funcionales cubren, entre otros aspectos:

- validación de una admisión activa;
- autenticación y autorización;
- aislamiento por tenant;
- liberación consistente de la cama origen;
- continuidad de la admisión durante un traslado;
- validación y ocupación de la cama destino;
- cierre de la admisión durante el alta;
- transición de la cama liberada hacia `limpieza`;
- atomicidad de los cambios principales;
- auditoría y trazabilidad del resultado.

Los requisitos no funcionales contemplan:

- consistencia transaccional;
- seguridad;
- aislamiento multitenant;
- control de concurrencia;
- sustituibilidad;
- mantenibilidad;
- trazabilidad;
- protección de datos.

Cada requisito posee un criterio de aceptación verificable. El detalle completo se encuentra en:

`trazabilidad/lsp-rf-rnf-criterios.md`

En el documento final se incorporará la tabla detallada de RF, RNF y sus respectivos criterios de aceptación.

## 4. Fundamento del principio LSP

Liskov Substitution Principle forma parte de los cinco principios SOLID.

Para esta actividad, LSP se interpreta como la necesidad de que una implementación derivada pueda utilizarse a través del contrato de su abstracción sin provocar un comportamiento incompatible con las expectativas definidas para dicha abstracción.

Aplicado al módulo ASII-08, no es suficiente declarar que `Traslado` y `Alta` pertenecen al mismo flujo. Para utilizar una abstracción común deben existir garantías compartidas que ambas operaciones puedan cumplir.

Una implementación concreta puede agregar reglas propias, pero no debe contradecir las garantías que el consumidor espera del contrato común.

Por ello, la evaluación de LSP se realiza considerando:

- precondiciones;
- poscondiciones;
- invariantes;
- responsabilidades;
- comportamiento del consumidor;
- necesidad o ausencia de condicionales por subtipo.

La fuente obligatoria utilizada para fundamentar el principio es MVP Cluster, en su material sobre principios básicos del diseño de software.

## 5. Diseño antes de aplicar LSP

El diseño inicial utiliza una abstracción denominada:

`SalidaPaciente`

Esta abstracción representa de forma demasiado específica el concepto de salida y supone que toda operación debe:

- cerrar la admisión;
- liberar la cama origen;
- dejar la cama directamente disponible;
- finalizar el proceso hospitalario.

Se consideran inicialmente dos clases derivadas:

- `AltaPaciente`;
- `TrasladoPaciente`.

El alta puede cumplir la expectativa de cerrar la admisión, aunque la disponibilidad inmediata de la cama continúa siendo una regla incorrecta porque la cama debe pasar primero por sanitización.

El problema principal se manifiesta en `TrasladoPaciente`.

Un traslado interno:

- no debe cerrar la admisión;
- mantiene activa la hospitalización;
- necesita una cama destino;
- debe validar la disponibilidad de esa cama;
- cambia la cama destino a `ocupada`;
- deja la cama origen en `limpieza`.

Por lo tanto, el traslado contradice varias expectativas de la abstracción `SalidaPaciente`.

### Problema de sustituibilidad

Si el coordinador recibe un objeto declarado como `SalidaPaciente`, espera que cualquier implementación respete el contrato de dicha abstracción.

Con el diseño anterior, el consumidor tendría que reconocer específicamente un traslado para modificar el comportamiento:

    if operacion es TrasladoPaciente:
        mantener admision activa
        validar cama destino
        ocupar cama destino
    else:
        cerrar admision

Esta necesidad de identificar el subtipo evidencia que la abstracción no representa correctamente las garantías compartidas.

El diseño genera además riesgos de consistencia, entre ellos:

- cierre incorrecto de una admisión durante un traslado;
- disponibilidad prematura de una cama;
- lógica condicional dependiente del subtipo;
- mayor acoplamiento entre coordinador e implementaciones concretas.

El archivo editable que representa este diseño es:

`diagramas/lsp-diseno-antes.puml`

Su representación visual corresponde a:

`diagramas/lsp-diseno-antes.png`

## 6. Diseño después de aplicar LSP

El rediseño sustituye la abstracción `SalidaPaciente` por un contrato más neutral:

`OperacionDisposicionPaciente`

La nueva abstracción contiene únicamente garantías que pueden ser respetadas por traslado y alta.

### Invariantes comunes

Toda implementación debe garantizar:

1. que la admisión exista y se encuentre activa antes de iniciar;
2. que el usuario esté autenticado y autorizado;
3. que la operación corresponda al tenant adecuado;
4. que la cama origen deje de estar ocupada por la admisión al completar la operación;
5. que la cama origen quede en estado `limpieza`;
6. que los cambios principales formen una operación atómica;
7. que un error previo a la confirmación produzca rollback;
8. que el resultado quede registrado para auditoría.

### OperacionTraslado

La implementación de traslado agrega únicamente reglas propias de su proceso:

- requiere cama destino;
- la cama destino debe ser distinta de la cama origen;
- la cama destino debe estar disponible;
- la disponibilidad debe revalidarse antes de confirmar;
- la cama destino pasa a `ocupada`;
- la admisión continúa activa;
- la admisión queda asociada con la nueva cama;
- la cama origen queda en `limpieza`.

### OperacionAlta

La implementación de alta agrega:

- validación de los datos del egreso;
- registro de fecha, tipo y observaciones;
- cierre de la admisión;
- liberación de la cama origen;
- transición de la cama origen hacia `limpieza`.

### Sustituibilidad obtenida

Después del rediseño, el coordinador puede trabajar con:

`OperacionDisposicionPaciente`

sin tener que conocer si la implementación concreta es un traslado o un alta para corregir su comportamiento.

Conceptualmente:

    procesar(OperacionDisposicionPaciente operacion):
        validar contexto comun
        iniciar transaccion
        operacion.ejecutar()
        registrar auditoria
        confirmar transaccion

La diferencia entre ambas operaciones permanece encapsulada en cada implementación concreta.

El archivo editable del diseño corregido es:

`diagramas/lsp-diseno-despues.puml`

Su representación visual corresponde a:

`diagramas/lsp-diseno-despues.png`

## 7. Responsabilidades y dependencias

### OperacionDisposicionPaciente

Su responsabilidad es definir el contrato común.

No debe decidir:

- si existe una cama destino;
- si la admisión debe mantenerse activa;
- si la admisión debe cerrarse;
- cuáles son los datos particulares del alta.

### OperacionTraslado

Su responsabilidad consiste en ejecutar las reglas exclusivas del traslado interno.

Incluye la validación y ocupación de la cama destino y la conservación de la admisión activa.

### OperacionAlta

Su responsabilidad consiste en ejecutar las reglas exclusivas del egreso hospitalario.

Incluye el registro de los datos de alta y el cierre de la admisión.

### Coordinador del flujo

El coordinador se encarga de las tareas comunes:

- validación del contexto;
- inicio y confirmación de la transacción;
- ejecución de la operación mediante la abstracción;
- coordinación de auditoría;
- tratamiento de errores comunes.

No debe incorporar condicionales destinados a reparar comportamientos incompatibles de los subtipos.

### Dependencias conceptuales

El flujo se relaciona con:

- repositorio de admisiones;
- repositorio de camas;
- historial de traslados;
- servicio de autorización;
- servicio de auditoría;
- administrador de transacciones.

La separación de estas responsabilidades reduce el acoplamiento del coordinador con los detalles particulares de traslado y alta.

## 8. Evidencia y validación

La actividad cuenta con evidencia técnica verificable.

### Fuentes editables

Se conservaron los siguientes artefactos:

- `diagramas/lsp-diseno-antes.puml`;
- `diagramas/lsp-diseno-despues.puml`;
- `trazabilidad/lsp-rf-rnf-criterios.md`;
- `trazabilidad/lsp-diseno-antes-despues.md`.

### Generación de diagramas

Los archivos PlantUML fueron procesados localmente mediante Java y PlantUML.

El diseño antes generó correctamente:

`diagramas/lsp-diseno-antes.png`

El diseño después generó correctamente:

`diagramas/lsp-diseno-despues.png`

Ambos procesos finalizaron sin errores de PlantUML.

### Validaciones realizadas

Se verificó que:

- los RF y RNF posean criterios de aceptación;
- traslado y alta compartan únicamente invariantes compatibles;
- el traslado no cierre la admisión;
- el alta sí cierre la admisión;
- la cama origen quede en `limpieza`;
- la cama destino sea utilizada únicamente durante un traslado;
- el diseño posterior permita trabajar mediante una abstracción común;
- los diagramas tengan fuentes editables;
- la evidencia utilice exclusivamente información ficticia;
- los cambios se encuentren registrados mediante Git.

La evidencia detallada se conserva en:

`evidencia/evidencia-lsp.md`

## 9. Evidencia Git, uso de IA y defensa

La actividad fue desarrollada en la rama:

`feature/lsp-traslados-altas-liberacion-camas`

El trabajo se dividió en commits con propósitos específicos para permitir verificar la evolución de la solución.

La evidencia Git incluye:

- historial legible;
- commits con propósito;
- rama independiente;
- referencia a la entrega UML anterior;
- árbol final de archivos;
- commit evaluado;
- etiqueta final;
- enlace verificable al repositorio.

La información final de commit y etiqueta será registrada al cerrar la entrega.

### Uso de inteligencia artificial

El uso de ChatGPT como herramienta de apoyo se documenta transparentemente en:

`DECLARACION_IA.md`

La declaración identifica:

- herramienta utilizada;
- propósito;
- consultas relevantes;
- partes aceptadas;
- partes revisadas o modificadas;
- validación humana.

### Defensa oral

La preparación para la defensa se encuentra en:

`defensa/guia-defensa-lsp.md`

La guía permite explicar:

- qué representa LSP;
- cuál era el problema del diseño inicial;
- qué cambió en el rediseño;
- cuáles son las invariantes comunes;
- cuáles son las reglas específicas de traslado y alta;
- cómo se demuestra la sustituibilidad;
- cómo modificar un artefacto editable durante la evaluación.

## 10. Conclusión

La actividad permitió transformar un diseño cuya abstracción imponía reglas incompatibles con el traslado en un modelo donde traslado y alta respetan un contrato común.

La decisión más relevante fue eliminar del contrato compartido las reglas que pertenecían exclusivamente a una de las operaciones. En particular, el cierre de la admisión no puede considerarse una garantía común, porque un traslado debe conservarla activa. De la misma forma, la existencia de una cama destino pertenece únicamente al traslado.

Como resultado, `OperacionTraslado` y `OperacionAlta` pueden utilizarse mediante `OperacionDisposicionPaciente` sin que el coordinador necesite identificar el subtipo para corregir su comportamiento. Esta característica constituye la principal evidencia de la aplicación de LSP.

La principal limitación del trabajo es que la mejora fue validada a nivel de diseño y artefactos académicos; no se implementó ni sometió a pruebas de carga o concurrencia dentro de un sistema hospitalario productivo.

El cumplimiento se respalda mediante requisitos y criterios de aceptación, diagramas editables antes/después, validación de PlantUML, historial Git, evidencia documental, declaración de IA y guía de defensa oral.

## 11. Bibliografía

MVP Cluster. “Diseño de software 2”. https://mvpcluster.com/diseno-de-software-2/ (consulta: 31 de julio de 2026).

No se incorporan referencias de implementación PHP o PDO debido a que esta actividad se concentra en diseño arquitectónico y no incluye una implementación ejecutable en PHP.