# Diseño antes/después — Aplicación de LSP

## 1. Identificación

- **Estudiante:** Esaú Abimael de la Cruz Mendoza
- **GitHub:** `00AbiMendoza`
- **Módulo:** Traslados, altas y liberación de camas
- **Flujo:** Traslado o alta del paciente con liberación consistente de cama
- **Principio aplicado:** Liskov Substitution Principle (LSP)

## 2. Problema de diseño

El flujo del módulo contiene dos operaciones relacionadas pero con efectos diferentes:

- traslado interno;
- alta hospitalaria.

Ambas operaciones parten de una admisión activa y liberan la cama de origen, pero no producen exactamente el mismo resultado.

En un traslado:

- la admisión continúa activa;
- se requiere una cama destino;
- la cama destino pasa a `ocupada`;
- la cama origen pasa a `limpieza`.

En un alta:

- no existe cama destino;
- la admisión se cierra;
- se registra información de egreso;
- la cama origen pasa a `limpieza`.

El problema aparece cuando el diseño utiliza una clase base que define comportamientos demasiado específicos y luego intenta reutilizarla para ambas operaciones.

## 3. Diseño antes

### 3.1 Estructura problemática

Se plantea inicialmente una clase base denominada `SalidaPaciente`.

Esta clase define las siguientes expectativas:

1. cerrar siempre la admisión;
2. liberar la cama actual;
3. marcar la cama liberada como disponible;
4. finalizar el proceso.

Posteriormente se crean dos clases derivadas:

- `AltaPaciente`;
- `TrasladoPaciente`.

La clase `AltaPaciente` puede cumplir parcialmente este comportamiento, pero `TrasladoPaciente` no puede sustituir correctamente a `SalidaPaciente`, porque:

- no debe cerrar la admisión;
- necesita una cama destino;
- debe mantener activa la hospitalización;
- la cama origen no debe pasar directamente a `disponible`;
- la cama destino debe convertirse en `ocupada`.

### 3.2 Violación de LSP

Si un componente consumidor recibe una instancia de `SalidaPaciente`, espera que cualquier subtipo respete el contrato definido por la clase base.

Sin embargo, al recibir `TrasladoPaciente`, el consumidor debe introducir condiciones como:

    si operacion es TrasladoPaciente:
        no cerrar admision
        validar cama destino
        ocupar cama destino
        mantener admision activa
    de lo contrario:
        cerrar admision

Esto demuestra que el subtipo no puede sustituir a la clase base sin alterar el comportamiento esperado.

El consumidor necesita conocer qué implementación concreta recibió para corregir las reglas del proceso. Esta dependencia rompe la sustituibilidad y aumenta el acoplamiento.

### 3.3 Consecuencias del diseño antes

- El contrato base contiene reglas que no son comunes a todos los subtipos.
- `TrasladoPaciente` debe contradecir comportamiento heredado.
- El consumidor necesita condicionales por tipo concreto.
- Se dificulta agregar nuevas operaciones relacionadas.
- Las pruebas deben contemplar excepciones artificiales causadas por la jerarquía.
- Existe riesgo de cerrar una admisión durante un traslado.
- Existe riesgo de marcar una cama como disponible sin sanitización.

## 4. Diseño después

### 4.1 Nueva abstracción

Se reemplaza `SalidaPaciente` por un contrato neutral denominado:

`OperacionDisposicionPaciente`

Este contrato define únicamente las condiciones que ambas operaciones pueden respetar.

### 4.2 Contrato común

Toda implementación de `OperacionDisposicionPaciente` debe garantizar:

1. recibir una admisión activa;
2. validar autorización y tenant;
3. ejecutar los cambios principales dentro de una transacción;
4. liberar la asociación de la cama de origen;
5. dejar la cama de origen en estado `limpieza`;
6. registrar trazabilidad del resultado;
7. realizar rollback ante un error previo al commit.

El contrato no obliga a:

- cerrar siempre la admisión;
- utilizar una cama destino;
- mantener siempre activa la admisión.

Estas reglas pertenecen a cada implementación concreta.

## 5. Implementaciones sustituibles

### 5.1 `OperacionTraslado`

Implementa el contrato común y además:

- valida que exista una cama destino;
- verifica que sea distinta de la cama origen;
- revalida que esté disponible;
- cambia la cama destino a `ocupada`;
- asocia la admisión con la cama destino;
- mantiene activa la admisión;
- deja la cama origen en `limpieza`.

### 5.2 `OperacionAlta`

Implementa el mismo contrato común y además:

- valida los datos requeridos para el egreso;
- registra fecha, tipo y observaciones;
- cierra la admisión;
- deja la cama origen en `limpieza`.

## 6. Aplicación correcta de LSP

Después del rediseño, un componente coordinador puede trabajar únicamente con el contrato:

    OperacionDisposicionPaciente

El consumidor no necesita preguntar si recibió un traslado o un alta para corregir comportamientos incompatibles.

Ejemplo conceptual:

    ejecutar(OperacionDisposicionPaciente operacion):
        validar contexto comun
        iniciar transaccion
        operacion.ejecutar()
        registrar auditoria
        confirmar transaccion

Tanto `OperacionTraslado` como `OperacionAlta` pueden sustituir al contrato sin romper las invariantes compartidas.

Cada implementación puede agregar sus reglas particulares, pero ninguna contradice las garantías comunes.

## 7. Comparación antes/después

| Aspecto | Diseño antes | Diseño después |
|---|---|---|
| Abstracción | `SalidaPaciente` | `OperacionDisposicionPaciente` |
| Contrato base | Define cierre de admisión y disponibilidad inmediata | Define solo invariantes realmente comunes |
| Traslado | Contradice reglas heredadas | Cumple el contrato y agrega reglas específicas |
| Alta | Se adapta al contrato rígido | Cumple el mismo contrato con comportamiento propio |
| Consumidor | Requiere condicionales por subtipo | Trabaja contra una abstracción común |
| Cama origen | Puede quedar directamente disponible | Siempre queda en `limpieza` |
| Admisión | Riesgo de cierre incorrecto | Cada implementación conserva su regla correcta |
| Extensibilidad | Baja | Mayor |
| Sustituibilidad | No garantizada | Garantizada por el contrato común |

## 8. Responsabilidades

### `OperacionDisposicionPaciente`

Responsabilidad:

- definir el contrato común que toda operación de disposición debe respetar.

No debe:

- decidir si existe una cama destino;
- decidir si la admisión se cierra;
- implementar detalles específicos de traslado o alta.

### `OperacionTraslado`

Responsabilidad:

- ejecutar las reglas exclusivas de un traslado interno.

### `OperacionAlta`

Responsabilidad:

- ejecutar las reglas exclusivas del alta hospitalaria.

### Coordinador del flujo

Responsabilidad:

- validar contexto común;
- controlar la transacción;
- ejecutar la operación mediante la abstracción;
- registrar auditoría.

No debe contener condicionales destinados a corregir el comportamiento de un subtipo.

## 9. Dependencias

El diseño propuesto requiere dependencias conceptuales hacia:

- repositorio de admisiones;
- repositorio de camas;
- repositorio o historial de traslados;
- servicio de autorización;
- servicio de auditoría;
- administrador de transacciones.

Estas dependencias apoyan las invariantes del flujo, pero no modifican el principio central: cualquier implementación concreta debe poder utilizarse mediante `OperacionDisposicionPaciente` sin romper el comportamiento esperado.

## 10. Evidencia verificable esperada

La aplicación de LSP será demostrada mediante:

- requisitos y criterios de aceptación;
- diagrama editable del diseño antes;
- diagrama editable del diseño después;
- comparación documentada;
- evidencia Git;
- historial de commits;
- declaración de uso de IA;
- guía de defensa oral.

## 11. Conclusión técnica

El rediseño elimina una jerarquía en la que el traslado estaba obligado a contradecir reglas definidas por una clase base demasiado específica.

La nueva abstracción conserva únicamente invariantes compartidas por traslado y alta. Como consecuencia, ambas implementaciones pueden sustituir al contrato común sin que el consumidor necesite identificar el subtipo ni corregir su comportamiento.

Esta sustituibilidad constituye la evidencia principal de la aplicación de LSP en el módulo ASII-08.