# Matriz de trazabilidad — ASII-08

## 1. Identificación

- **Módulo:** ASII-08 — Traslados, altas y liberación de camas
- **Proceso:** Traslado o alta del paciente con liberación consistente de cama
- **Fuentes editables:** `casos-de-uso.puml`, `actividad.puml` y `secuencia.puml`
- **Datos utilizados:** exclusivamente ficticios

## 2. Requisitos funcionales

| ID | Diagrama de casos de uso | Diagrama de actividad | Diagrama de secuencia | Cobertura |
|---|---|---|---|---|
| RF-01 | Validar operación, autorización y tenant | Consultar la admisión `ADM-DEM-001`; decisión sobre admisión activa y tenant | `consultarAdmisiónActiva(ADM-DEM-001)` | Completa |
| RF-02 | Gestionar traslado interno; Registrar alta hospitalaria | Decisión `¿Tipo de operación?` | Fragmentos alternativos `Operación de traslado` y `Operación de alta` | Completa |
| RF-03 | Validar operación, autorización y tenant | Autenticar al usuario; validar permisos | `validarAcceso(token, operación, tenant)` | Completa |
| RF-04 | Validar operación, autorización y tenant | Validar hospital y tenant; validar destino del mismo tenant | Consulta de admisión, destino, estado y tenant | Completa |
| RF-05 | Gestionar traslado interno | Decisión `¿Destino distinto, disponible y del mismo tenant?` | `validarDestino(CAM-B-204)` | Completa |
| RF-06 | Gestionar traslado interno | Validar y revalidar disponibilidad de la cama destino | `validarDestino()` y `revalidar y ejecutar traslado` | Completa |
| RF-07 | Gestionar traslado interno | Relacionar admisión con `CAM-B-204` | `revalidar y ejecutar traslado`; nota `Admisión: nueva cama` | Completa |
| RF-08 | Gestionar traslado interno | Marcar `CAM-B-204` como ocupada | Nota de resultado `CAM-B-204: ocupada` | Completa |
| RF-09 | Gestionar traslado interno; Registrar alta hospitalaria | Marcar `CAM-A-101` como limpieza | Notas de traslado y alta con `CAM-A-101: limpieza` | Completa |
| RF-10 | Registrar alta hospitalaria | Validar datos obligatorios; cerrar `ADM-DEM-001` | `validarDatosDelAlta()` y `cerrarAdmisiónYMarcarLimpieza()` | Completa |
| RF-11 | Confirmar sanitización de cama | Cambiar estado de limpieza a disponible después de confirmación válida | `cambiarLimpiezaADisponible()` | Completa |
| RF-12 | Registrar auditoría y notificar resultado | Registrar operaciones aceptadas, rechazadas y errores | Mensajes `registrarTraslado`, `registrarAlta`, `registrarRechazo`, `registrarError` y `registrarSanitización` | Completa |
| RF-13 | Casos principales y consulta de operaciones | Notificaciones de éxito, rechazo y error | Respuestas de la interfaz al usuario y al personal de sanitización | Completa |
| RF-14 | Registrar auditoría y notificar resultado | Notificar operación exitosa, rechazo, reversión y cama disponible | Participante `Notificaciones` y mensajes correspondientes | Completa |
| RF-15 | Registrar auditoría y notificar resultado | Iniciar, confirmar o revertir transacción | `iniciarTransacción`, `confirmarTransacción` y `revertirTransacción` | Completa |

## 3. Reglas de negocio

| ID | Elemento UML relacionado | Cobertura |
|---|---|---|
| RN-01 | Validación de admisión activa en actividad y secuencia | Completa |
| RN-02 | Validación de tenant en los tres diagramas | Completa |
| RN-03 | Decisión de destino distinto en actividad y validación de destino en secuencia | Completa |
| RN-04 | Validación de disponibilidad de la cama destino | Completa |
| RN-05 | En traslado solo se actualiza la cama asociada; no se cierra la admisión | Completa |
| RN-06 | En alta se ejecuta `cerrarAdmisiónYMarcarLimpieza()` | Completa |
| RN-07 | La cama liberada pasa primero a limpieza y después a disponible | Completa |
| RN-08 | Validación de permiso para confirmar sanitización | Completa |
| RN-09 | Ramas de error con reversión de la transacción | Completa |
| RN-10 | Leyendas y notas con identificadores ficticios `PAC-DEM-001`, `ADM-DEM-001`, `HOSP-DEMO-01`, `CAM-A-101` y `CAM-B-204` | Completa |

## 4. Excepciones

| ID | Diagrama de actividad | Diagrama de secuencia | Respuesta representada | Cobertura |
|---|---|---|---|---|
| EX-01 | Autenticación inválida | Fragmento `Acceso inválido` | Error 401/403, rechazo y auditoría | Completa |
| EX-02 | Usuario sin autorización | Validación de acceso y respuesta 403 | Operación denegada y registrada | Completa |
| EX-03 | Admisión inexistente o inactiva | Fragmento `Admisión inválida o de otro tenant` | No se procesa la operación | Completa |
| EX-04 | Admisión o cama de otro tenant | Validaciones de tenant | Rechazo y error de validación | Completa |
| EX-05 | Destino igual a la cama actual | Validación de destino distinto | Traslado rechazado | Completa |
| EX-06 | Destino ocupado, en limpieza o mantenimiento | Validación de disponibilidad | Mostrar cama no disponible | Completa |
| EX-07 | Datos del alta incompletos | Fragmento `Datos incompletos` | Mostrar datos requeridos sin cerrar la admisión | Completa |
| EX-08 | Sanitización por personal no autorizado | Fragmento `Confirmación inválida` | Mantener cama en limpieza y registrar rechazo | Completa |
| EX-09 | Cambio concurrente de la cama destino | Fragmento `Error técnico o de concurrencia` | Revertir transacción y notificar indisponibilidad | Completa |
| EX-10 | Error en admisión, camas o auditoría | Ramas de error transaccional | Revertir cambios y mostrar error controlado | Completa |
| EX-11 | La notificación se ejecuta después de confirmar la transacción o el cambio principal | Los fragmentos de traslado, alta y sanitización registran el fallo y programan un reintento | Los cambios principales permanecen confirmados y el fallo queda auditado para reintento | Completa |

## 5. Resultados finales trazados

| Resultado | Casos de uso | Actividad | Secuencia |
|---|---|---|---|
| Traslado completado | Gestionar traslado interno | Destino ocupada, origen en limpieza y admisión asociada | Confirmar transacción y notificar traslado |
| Alta completada | Registrar alta hospitalaria | Admisión cerrada y cama en limpieza | Confirmar transacción y notificar alta |
| Sanitización confirmada | Confirmar sanitización de cama | Limpieza a disponible | Cambiar estado y notificar cama disponible |
| Operación rechazada | Validación y consulta de excepciones | Terminaciones por validación fallida | Fragmentos alternativos de rechazo |
| Error transaccional | Auditoría y resultado | Revertir transacción | `revertirTransacción()` y error de operación |

## 6. Validación final

La excepción **EX-11** quedó completamente representada en el diagrama de secuencia. Cuando falla una notificación después de completar la operación principal, el sistema conserva los cambios confirmados, registra el fallo en auditoría y programa un reintento.

La matriz no presenta brechas pendientes entre los requisitos definidos y los elementos UML elaborados.