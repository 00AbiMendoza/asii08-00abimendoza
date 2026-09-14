# Guía de defensa oral — Aplicación de LSP

## 1. Presentación

Mi trabajo corresponde al módulo ASII-08 — Traslados, altas y liberación de camas.

La actividad consiste en aplicar el principio SOLID Liskov Substitution Principle, o LSP, al flujo de traslado o alta del paciente con liberación consistente de cama.

## 2. ¿Qué es LSP?

LSP indica que un subtipo debe poder sustituir al tipo base sin provocar un comportamiento incorrecto.

En términos prácticos, si un componente trabaja con una abstracción, cualquier implementación válida de esa abstracción debe respetar el contrato esperado.

## 3. Problema del diseño antes

En el diseño inicial se utilizó una clase base llamada:

`SalidaPaciente`

Esta clase asumía que toda salida debía:

- cerrar la admisión;
- liberar la cama actual;
- dejar la cama directamente disponible.

El problema es que estas reglas no son válidas para todas las operaciones.

## 4. ¿Por qué el traslado viola LSP en el diseño anterior?

Un traslado:

- no cierra la admisión;
- mantiene activa la hospitalización;
- necesita una cama destino;
- ocupa la cama destino;
- deja la cama origen en limpieza.

Por lo tanto, `TrasladoPaciente` tendría que contradecir varias reglas heredadas desde `SalidaPaciente`.

El consumidor también necesitaría preguntar qué tipo de operación recibió para corregir el comportamiento.

Ejemplo conceptual:

    if operacion es TrasladoPaciente:
        mantener admision activa
        validar cama destino
    else:
        cerrar admision

Cuando el consumidor necesita reconocer el subtipo para corregir su comportamiento, la sustituibilidad no está funcionando correctamente.

## 5. Diseño después

Se reemplazó la abstracción anterior por:

`OperacionDisposicionPaciente`

Esta nueva abstracción contiene únicamente reglas que traslado y alta pueden respetar.

## 6. Invariantes comunes

Tanto traslado como alta deben cumplir:

1. La admisión existe y está activa antes de la operación.
2. El usuario está autenticado y autorizado.
3. La operación pertenece al tenant correcto.
4. La cama origen deja de estar ocupada por la admisión.
5. La cama origen queda en estado `limpieza`.
6. Los cambios principales se ejecutan dentro de una transacción.
7. Si ocurre un error antes de confirmar, se realiza rollback.
8. El resultado queda registrado en auditoría.

## 7. Reglas específicas del traslado

`OperacionTraslado` agrega:

- existencia de cama destino;
- destino distinto de la cama origen;
- cama destino disponible;
- destino pasa a `ocupada`;
- admisión continúa activa;
- admisión queda asociada a la cama destino.

Estas reglas no contradicen el contrato común.

## 8. Reglas específicas del alta

`OperacionAlta` agrega:

- validación de información del egreso;
- registro de fecha, tipo y observaciones;
- cierre de la admisión;
- cama origen en `limpieza`.

Estas reglas tampoco contradicen el contrato común.

## 9. ¿Dónde se demuestra LSP?

La demostración principal consiste en que el coordinador puede recibir:

`OperacionDisposicionPaciente`

y ejecutar cualquiera de sus implementaciones sin tener que corregirlas mediante condiciones por tipo.

Por ejemplo:

    procesar(OperacionDisposicionPaciente operacion):
        validar contexto comun
        iniciar transaccion
        operacion.ejecutar()
        registrar auditoria
        confirmar transaccion

El mismo consumidor puede trabajar con `OperacionTraslado` o `OperacionAlta`.

## 10. Diferencia entre el antes y el después

### Antes

- la clase base tenía reglas demasiado específicas;
- el traslado contradecía el comportamiento heredado;
- el consumidor necesitaba conocer el subtipo;
- existía riesgo de cerrar incorrectamente la admisión;
- la cama podía quedar disponible sin sanitización.

### Después

- el contrato contiene únicamente invariantes comunes;
- traslado y alta conservan sus reglas específicas;
- el consumidor trabaja contra una abstracción;
- no necesita condiciones para corregir subtipos;
- la cama origen siempre queda primero en `limpieza`.

## 11. Preguntas probables

### ¿Cuál fue el problema principal?

La abstracción original asumía comportamientos que no eran comunes al traslado y al alta.

### ¿Qué cambió para aplicar LSP?

Se creó un contrato más neutral con únicamente invariantes compartidas.

### ¿Por qué traslado y alta pueden usar la misma abstracción?

Porque ambos respetan las garantías comunes, aunque tengan poscondiciones específicas diferentes.

### ¿Un traslado cierra la admisión?

No. La admisión continúa activa y cambia su cama asignada.

### ¿Un alta cierra la admisión?

Sí. El alta registra el egreso y finaliza la admisión.

### ¿Qué pasa con la cama origen?

Tanto en traslado como en alta queda en estado `limpieza`.

### ¿Por qué no queda disponible inmediatamente?

Porque debe realizarse y confirmarse la sanitización antes de volver a utilizarla.

### ¿Cómo se demuestra la sustituibilidad?

El coordinador puede utilizar una instancia de traslado o de alta mediante `OperacionDisposicionPaciente` sin conocer el subtipo para corregir su comportamiento.

### ¿Qué requisitos respaldan el diseño?

Los RF, RNF y criterios de aceptación se encuentran en:

`trazabilidad/lsp-rf-rnf-criterios.md`

### ¿Dónde está explicado el diseño antes y después?

En:

`trazabilidad/lsp-diseno-antes-despues.md`

### ¿Dónde están los diagramas editables?

En:

- `diagramas/lsp-diseno-antes.puml`
- `diagramas/lsp-diseno-despues.puml`

## 12. Modificación posible durante la defensa

Si se solicita agregar una nueva operación de disposición, por ejemplo una operación ficticia de transferencia externa, primero se debe verificar si puede cumplir las invariantes de `OperacionDisposicionPaciente`.

No debe agregarse como implementación únicamente porque tenga un nombre relacionado con una salida.

La nueva operación debe respetar el contrato común para no introducir nuevamente una violación de LSP.

## 13. Explicación corta para exposición

El problema inicial era que una clase base llamada `SalidaPaciente` obligaba a cerrar la admisión y dejar la cama disponible. Eso funcionaba parcialmente para el alta, pero no para un traslado, porque el traslado mantiene la admisión activa, ocupa otra cama y deja la anterior en limpieza.

Para corregirlo apliqué LSP creando `OperacionDisposicionPaciente`, que contiene únicamente las reglas comunes a ambas operaciones. Después, `OperacionTraslado` y `OperacionAlta` implementan sus reglas particulares sin contradecir ese contrato.

De esta forma, el coordinador puede trabajar con cualquiera de las dos implementaciones sin necesitar saber cuál recibió para corregir su comportamiento.

## 14. Cierre

La decisión más importante fue separar las invariantes comunes de las reglas específicas de traslado y alta.

La evidencia del cumplimiento de LSP está en que ambas implementaciones pueden utilizarse mediante el mismo contrato sin romper las expectativas del consumidor.