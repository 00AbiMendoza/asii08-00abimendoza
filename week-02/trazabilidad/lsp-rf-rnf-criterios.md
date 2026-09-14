# RF, RNF y criterios de aceptación — Aplicación de LSP

## 1. Identificación

- **Estudiante:** Esaú Abimael de la Cruz Mendoza
- **GitHub:** `00AbiMendoza`
- **Módulo:** Traslados, altas y liberación de camas
- **Flujo evaluado:** Traslado o alta del paciente con liberación consistente de cama
- **Principio SOLID asignado:** Liskov Substitution Principle (LSP)
- **Datos utilizados:** exclusivamente ficticios

## 2. Propósito

Este documento delimita los requisitos funcionales, requisitos no funcionales y criterios de aceptación utilizados para evaluar el rediseño del flujo de traslado o alta hospitalaria.

Los criterios se concentran en las reglas comunes que deben conservarse independientemente de si la operación concreta corresponde a un traslado o a un alta. Estas reglas servirán posteriormente como base para justificar la aplicación del principio LSP.

## 3. Requisitos funcionales

| ID | Requisito funcional | Criterio de aceptación |
|---|---|---|
| RF-LSP-01 | El sistema debe permitir ejecutar una operación únicamente sobre una admisión activa. | Dada una admisión activa del tenant actual, cuando se solicita un traslado o alta autorizado, el proceso puede continuar. Si la admisión está cerrada o no existe, no se modifica ningún dato. |
| RF-LSP-02 | El sistema debe validar identidad, autorización y tenant antes de modificar la admisión o las camas. | Una solicitud sin autenticación, permiso suficiente o con un tenant diferente debe ser rechazada antes de ejecutar cambios. |
| RF-LSP-03 | Tanto el traslado como el alta deben liberar consistentemente la cama de origen. | Al finalizar cualquiera de las dos operaciones, la cama de origen deja de estar ocupada por el paciente y queda en estado `limpieza`. |
| RF-LSP-04 | El traslado interno debe conservar activa la admisión. | Después de un traslado exitoso, la admisión continúa activa y queda asociada con la nueva cama. |
| RF-LSP-05 | El traslado interno debe ocupar únicamente una cama destino disponible y válida. | La cama destino debe ser distinta de la cama origen, pertenecer al tenant actual y encontrarse `disponible`; al confirmar el traslado pasa a `ocupada`. |
| RF-LSP-06 | El alta hospitalaria debe cerrar la admisión. | Después de un alta exitosa se registra la fecha y la información de egreso, y la admisión deja de estar activa. |
| RF-LSP-07 | Una cama liberada no debe pasar directamente a `disponible`. | Después de un traslado o alta, la cama origen permanece en `limpieza` hasta que se confirme su sanitización. |
| RF-LSP-08 | La operación debe ser atómica respecto de los cambios principales. | Si falla la actualización de la admisión, cama origen, cama destino o historial requerido, todos los cambios de la operación deben revertirse. |
| RF-LSP-09 | El sistema debe registrar trazabilidad de las operaciones aceptadas y rechazadas. | Cada intento relevante conserva al menos tipo de operación, resultado, usuario, tenant y referencia ficticia de las entidades afectadas. |
| RF-LSP-10 | El sistema debe devolver un resultado coherente con el tipo concreto de operación. | El traslado informa continuidad de la admisión y nueva cama; el alta informa cierre de la admisión. En ambos casos se informa que la cama origen quedó en limpieza. |

## 4. Requisitos no funcionales

| ID | Requisito no funcional | Criterio de aceptación |
|---|---|---|
| RNF-LSP-01 | Consistencia transaccional. | Ninguna ejecución debe dejar una admisión y sus camas relacionadas en estados parciales cuando ocurre un error antes de confirmar la transacción. |
| RNF-LSP-02 | Seguridad y autorización. | Todas las operaciones requieren autenticación y autorización antes de modificar información del módulo. |
| RNF-LSP-03 | Aislamiento multitenant. | Una operación solo puede consultar o modificar admisiones y camas pertenecientes al tenant autenticado. |
| RNF-LSP-04 | Control de concurrencia. | Dos solicitudes concurrentes no deben confirmar la asignación de la misma cama destino a admisiones diferentes. |
| RNF-LSP-05 | Sustituibilidad del diseño. | Cualquier implementación concreta aceptada por el contrato común de disposición del paciente debe respetar las precondiciones, poscondiciones e invariantes comunes sin obligar al consumidor a identificar el subtipo para corregir su comportamiento. |
| RNF-LSP-06 | Mantenibilidad. | Las reglas exclusivas del traslado y del alta deben permanecer en sus implementaciones correspondientes, evitando condicionales externos que contradigan el contrato común. |
| RNF-LSP-07 | Trazabilidad. | Las operaciones y errores relevantes deben poder relacionarse con su usuario, tenant, momento y resultado utilizando únicamente datos ficticios en la evidencia académica. |
| RNF-LSP-08 | Protección de datos. | Diagramas, pruebas, documentación y evidencia no deben incluir información clínica identificable ni credenciales reales. |

## 5. Invariantes comunes del flujo

Las siguientes condiciones deben permanecer verdaderas tanto para una operación de traslado como para una operación de alta:

1. La admisión debe existir y encontrarse activa antes de iniciar la operación.
2. El usuario debe estar autenticado y autorizado.
3. La operación debe ejecutarse dentro del tenant correspondiente.
4. La cama de origen debe dejar de estar ocupada por la admisión al completar la operación.
5. La cama de origen debe quedar en estado `limpieza`.
6. Los cambios principales deben ejecutarse de forma atómica.
7. Un error previo a la confirmación debe provocar rollback.
8. El resultado debe quedar trazado mediante auditoría.

## 6. Comportamientos específicos

### 6.1 Traslado

Además de las invariantes comunes:

- requiere una cama destino;
- la cama destino debe estar disponible;
- la admisión continúa activa;
- la admisión queda asociada a la cama destino;
- la cama destino pasa a `ocupada`.

### 6.2 Alta

Además de las invariantes comunes:

- no requiere una cama destino;
- registra la información del egreso;
- cierra la admisión;
- conserva la cama liberada en `limpieza`.

## 7. Criterio global de aceptación para LSP

El diseño se considerará compatible con LSP cuando el componente consumidor pueda ejecutar una operación mediante un contrato común sin necesitar condiciones especiales para corregir el comportamiento de `Traslado` o `Alta`.

Cada implementación concreta deberá cumplir las invariantes comunes y podrá agregar únicamente sus reglas específicas sin debilitar precondiciones, romper poscondiciones ni producir un estado incompatible con el contrato compartido.

## 8. Relación con la evidencia previa

Los requisitos de esta actividad derivan del comportamiento ya documentado para ASII-08 en:

- `trazabilidad/requisitos-y-excepciones.md`
- `trazabilidad/matriz-trazabilidad.md`
- `diagramas/casos-de-uso.puml`
- `diagramas/actividad.puml`
- `diagramas/secuencia.puml`

Los archivos anteriores permanecen sin modificaciones para conservar la trazabilidad de la entrega UML previa.