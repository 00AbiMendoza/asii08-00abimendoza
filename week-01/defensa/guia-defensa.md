# Guía de defensa oral — ASII-08

## 1. Presentación

Mi nombre es Esaú Abimael de la Cruz Mendoza. El trabajo corresponde al módulo ASII-08 — Traslados, altas y liberación de camas.

El proceso modelado comprende el traslado interno o el alta hospitalaria de un paciente, junto con la liberación consistente de la cama utilizada.

## 2. Objetivo

El objetivo fue representar:

- los actores involucrados;
- las operaciones principales;
- las validaciones necesarias;
- los cambios en la admisión y las camas;
- las excepciones;
- la auditoría;
- las transacciones;
- las notificaciones.

## 3. Diagrama de casos de uso

Este diagrama muestra las funciones del módulo desde la perspectiva de los actores.

Actores principales:

- Médico.
- Enfermera.
- Administrador hospitalario.
- Personal de sanitización.
- Sistema de autenticación y autorización.
- Sistema de auditoría.
- Sistema de notificaciones.

Casos principales:

- Gestionar traslado interno.
- Registrar alta hospitalaria.
- Confirmar sanitización de cama.
- Validar autorización y tenant.
- Registrar auditoría.
- Notificar resultados.

La confirmación de sanitización se modeló por separado porque una cama liberada no debe pasar directamente de ocupada a disponible.

## 4. Diagrama de actividad

El diagrama de actividad muestra el flujo, las decisiones, las excepciones y los resultados.

### Traslado interno

1. Autenticar al usuario.
2. Validar rol, permiso y tenant.
3. Consultar la admisión activa.
4. Seleccionar una cama destino distinta.
5. Validar que la cama esté disponible.
6. Iniciar una transacción.
7. Revalidar la cama destino.
8. Asociar la admisión con la cama destino.
9. Marcar la cama destino como ocupada.
10. Marcar la cama origen como limpieza.
11. Registrar auditoría.
12. Confirmar o revertir la transacción.
13. Notificar el resultado.

### Alta hospitalaria

1. Validar los datos del alta.
2. Iniciar una transacción.
3. Cerrar la admisión.
4. Registrar fecha, tipo y observaciones.
5. Marcar la cama liberada como limpieza.
6. Registrar auditoría.
7. Confirmar o revertir la transacción.
8. Notificar el resultado.

### Sanitización

1. Validar al personal autorizado.
2. Verificar que la cama esté en limpieza.
3. Cambiar la cama a disponible.
4. Registrar auditoría.
5. Notificar que la cama puede utilizarse.

## 5. Diagrama de secuencia

El diagrama de secuencia muestra el orden temporal de los mensajes.

Participantes:

- Usuario autorizado.
- Interfaz.
- Servicio ASII-08.
- Autenticación y autorización.
- Base de datos hospitalaria.
- Auditoría.
- Notificaciones.
- Personal de sanitización.

Validaciones principales:

- token válido;
- rol y permiso;
- tenant correcto;
- admisión activa;
- cama destino distinta;
- cama destino disponible;
- datos de alta completos;
- permiso para confirmar sanitización.

## 6. Uso de transacciones

Las actualizaciones de la admisión, las camas y la auditoría deben ejecutarse como una sola transacción.

Cuando falla una actualización principal, todos los cambios se revierten para mantener el estado anterior.

## 7. Concurrencia

La cama destino se valida antes de iniciar la operación y se revalida dentro de la transacción.

Esto evita que dos usuarios asignen simultáneamente la misma cama.

Cuando la cama deja de estar disponible:

- se cancela la operación;
- se revierte la transacción;
- se registra el error;
- se solicita otra cama.

## 8. Fallo de notificación

La notificación ocurre después de confirmar la operación principal.

Cuando falla:

1. la transacción principal no se revierte;
2. el fallo se registra en auditoría;
3. se programa un reintento;
4. la operación conserva su resultado.

## 9. Aislamiento por tenant

El usuario, la admisión, el paciente y las camas deben pertenecer al mismo tenant.

Esta regla impide que un usuario opere sobre información de otro hospital u organización.

## 10. Datos ficticios

Se utilizaron únicamente identificadores demostrativos:

- PAC-DEM-001
- ADM-DEM-001
- HOSP-DEMO-01
- CAM-A-101
- CAM-B-204

No se utilizaron datos clínicos reales ni información identificable.

## 11. Trazabilidad

La matriz relaciona:

- requisitos funcionales;
- reglas de negocio;
- excepciones;
- diagramas;
- elementos UML específicos.

La revisión final determinó cobertura completa para los requisitos y excepciones definidos.

## 12. Preguntas probables

### ¿Cuál es la diferencia entre traslado y alta?

El traslado mantiene activa la admisión y cambia la cama asignada. El alta cierra la admisión y libera la cama actual.

### ¿Por qué la cama pasa a limpieza?

Porque debe sanitizarse antes de volver a estar disponible.

### ¿Por qué se utiliza una transacción?

Para evitar que una operación quede aplicada parcialmente.

### ¿Por qué se revalida la cama destino?

Porque otro usuario podría ocuparla después de la primera validación.

### ¿Qué ocurre cuando falla una notificación?

La operación principal permanece confirmada. El fallo se audita y se programa un reintento.

### ¿Qué archivos permiten modificar los diagramas?

Los archivos con extensión .puml ubicados en la carpeta diagramas.

### ¿Cómo se verificó la cobertura?

Mediante el archivo trazabilidad/matriz-trazabilidad.md.

### ¿Cómo se documentó el uso de IA?

Mediante el archivo DECLARACION_IA.md.

## 13. Modificación durante la defensa

Para agregar un actor en PlantUML se utiliza una instrucción similar a:

    actor "Nuevo actor" as NuevoActor

Para agregar una relación:

    NuevoActor --> UC_Operacion

Para agregar una decisión en actividad:

    if (¿Validación correcta?) then (Sí)
      :Continuar operación;
    else (No)
      :Registrar rechazo;
      stop
    endif

Para agregar una alternativa en secuencia:

    alt Condición válida
      Modulo --> UI: Operación permitida
    else Condición inválida
      Modulo -> Auditoria: registrarRechazo()
      Modulo --> UI: Mostrar error
    end

Después de modificar un archivo .puml, deben regenerarse sus versiones PNG y SVG con PlantUML.

## 14. Cierre

Los tres diagramas representan perspectivas complementarias:

- casos de uso: actores y objetivos;
- actividad: flujo, decisiones y excepciones;
- secuencia: mensajes, validaciones y respuestas.

El trabajo mantiene coherencia entre requisitos, reglas de negocio, excepciones y elementos UML.