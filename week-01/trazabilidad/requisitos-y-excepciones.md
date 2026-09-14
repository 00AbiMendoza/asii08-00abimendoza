# Requisitos y excepciones — ASII-08

## 1. Actores comunes

Los tres diagramas UML utilizarán los mismos actores principales:

- **Médico:** solicita o registra el alta hospitalaria cuando corresponde.
- **Enfermera:** solicita o ejecuta un traslado interno según autorización.
- **Administrador hospitalario:** consulta y supervisa operaciones excepcionales.
- **Personal de sanitización:** confirma que una cama fue limpiada y puede volver a estar disponible.
- **Sistema de autenticación y autorización:** valida identidad, rol y permisos.
- **Sistema de auditoría:** conserva evidencia de operaciones exitosas y rechazadas.
- **Sistema de notificaciones:** informa el resultado de la operación a los responsables.

## 2. Requisitos funcionales

| ID | Requisito |
|---|---|
| RF-01 | El sistema debe permitir seleccionar una admisión activa del tenant actual. |
| RF-02 | El sistema debe permitir elegir entre traslado interno y alta hospitalaria. |
| RF-03 | El sistema debe validar la identidad, el rol y los permisos del usuario. |
| RF-04 | El sistema debe validar que la admisión, el paciente y las camas pertenezcan al mismo tenant. |
| RF-05 | Para un traslado interno, el sistema debe validar que la cama destino sea distinta de la cama actual. |
| RF-06 | Para un traslado interno, el sistema debe validar que la cama destino esté en estado `disponible`. |
| RF-07 | En un traslado interno exitoso, el sistema debe asociar la admisión con la cama destino. |
| RF-08 | En un traslado interno exitoso, la cama destino debe pasar a estado `ocupada`. |
| RF-09 | Después de un traslado o alta, la cama liberada debe pasar a estado `limpieza`. |
| RF-10 | En un alta exitosa, el sistema debe cerrar la admisión y registrar la fecha, el tipo y las observaciones del egreso. |
| RF-11 | La cama en estado `limpieza` solo debe pasar a `disponible` después de confirmar la sanitización. |
| RF-12 | El sistema debe registrar en auditoría toda operación exitosa o rechazada. |
| RF-13 | El sistema debe devolver una respuesta comprensible con el resultado de la operación. |
| RF-14 | El sistema debe notificar a los responsables cuando la operación finalice o requiera atención. |
| RF-15 | Las actualizaciones de admisión, cama origen, cama destino y auditoría deben ejecutarse como una sola transacción. |

## 3. Reglas de negocio

| ID | Regla |
|---|---|
| RN-01 | Solo puede trasladarse o darse de alta una admisión activa. |
| RN-02 | Un usuario solo puede operar dentro de su tenant. |
| RN-03 | La cama destino no puede ser la misma cama actual. |
| RN-04 | Una cama en estado `ocupada`, `limpieza` o `mantenimiento` no puede utilizarse como destino. |
| RN-05 | Un traslado interno no cierra la admisión. |
| RN-06 | Un alta hospitalaria sí cierra la admisión. |
| RN-07 | Una cama liberada nunca pasa directamente de `ocupada` a `disponible`. |
| RN-08 | Solo personal autorizado puede confirmar la sanitización. |
| RN-09 | Si una parte de la operación falla, todos los cambios deben revertirse. |
| RN-10 | No se deben exponer datos clínicos identificables en diagramas, ejemplos ni evidencias. |


## 4. Excepciones y respuestas esperadas

| ID | Excepción | Respuesta esperada |
|---|---|---|
| EX-01 | El usuario no está autenticado o el token no es válido. | Rechazar la solicitud, devolver error de autenticación y registrar el intento. |
| EX-02 | El usuario no posee el rol o el permiso requerido. | Denegar la operación, informar que no existe autorización y registrar auditoría. |
| EX-03 | La admisión no existe o no está activa. | No ejecutar el traslado o alta, mostrar un mensaje comprensible y registrar el rechazo. |
| EX-04 | La admisión, el paciente o una cama pertenecen a un tenant diferente. | Cancelar la operación, devolver error de aislamiento y registrar la incidencia. |
| EX-05 | La cama destino es la misma cama actual. | Rechazar el traslado y solicitar una cama distinta. |
| EX-06 | La cama destino está `ocupada`, `limpieza` o `mantenimiento`. | No realizar el traslado, informar el estado actual y solicitar otra cama. |
| EX-07 | Los datos del alta están incompletos. | Solicitar fecha, tipo y observaciones requeridas sin cerrar la admisión. |
| EX-08 | Un usuario no autorizado intenta confirmar la sanitización. | Denegar la confirmación, mantener la cama en `limpieza` y registrar auditoría. |
| EX-09 | La cama destino cambia de estado durante la operación por una solicitud concurrente. | Revalidar el estado, cancelar la transacción y notificar que la cama ya no está disponible. |
| EX-10 | Ocurre un error al actualizar la admisión, las camas o crear el registro de auditoría. | Revertir todos los cambios de la transacción, mantener el estado anterior y devolver un error controlado. |
| EX-11 | Falla el envío de la notificación después de completar la operación principal. | Mantener confirmados los cambios transaccionales, registrar el fallo de notificación y permitir su reintento. |

## 5. Resultados finales posibles

- **Traslado completado:** la admisión continúa activa en la cama destino, esta queda `ocupada` y la cama origen queda en `limpieza`.
- **Alta completada:** la admisión queda cerrada y la cama liberada queda en `limpieza`.
- **Sanitización confirmada:** la cama pasa de `limpieza` a `disponible`.
- **Operación rechazada:** no se alteran la admisión ni el estado de las camas y se conserva evidencia de auditoría.
- **Error transaccional:** se revierten todos los cambios y se mantiene el estado anterior.
