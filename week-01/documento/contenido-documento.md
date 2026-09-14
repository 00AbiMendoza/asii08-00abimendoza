# Diagramas UML del módulo ASII-08

## Portada

Los datos oficiales se encuentran en `documento/datos-portada.md`.

## Índice

1. Introducción
2. Descripción del módulo ASII-08
3. Requisitos, reglas de negocio y excepciones
4. Diagrama de casos de uso
5. Diagrama de actividad
6. Diagrama de secuencia
7. Matriz de trazabilidad
8. Uso de inteligencia artificial
9. Evidencias de Git y GitHub
10. Conclusión
11. Bibliografía

## 1. Introducción

El Lenguaje Unificado de Modelado permite representar de manera visual la estructura y el comportamiento de un sistema antes de su implementación. En este trabajo se modela el módulo ASII-08, correspondiente a traslados, altas y liberación de camas dentro de un sistema hospitalario.

Para describir el proceso se elaboraron diagramas de casos de uso, actividad y secuencia. Estos diagramas representan los actores involucrados, las operaciones disponibles, las validaciones de seguridad, las reglas de negocio, las excepciones y los cambios de estado que deben realizarse sobre las admisiones y las camas.

El modelado también considera el aislamiento por tenant, el control de concurrencia, el uso de transacciones, el registro de auditoría y el manejo de fallos de notificación. Todos los ejemplos e identificadores utilizados son ficticios y no contienen información clínica identificable.

## 2. Descripción del módulo ASII-08

El módulo ASII-08 administra los traslados internos, las altas hospitalarias y la liberación de camas. Su finalidad es mantener consistentes la admisión del paciente, la cama asignada y los estados operativos de las camas.

Durante un traslado interno, la admisión continúa activa y se asocia con una cama destino disponible. La cama destino cambia al estado ocupada, mientras que la cama anterior pasa al estado limpieza.

Durante un alta hospitalaria, la admisión se cierra y se registran la fecha, el tipo y las observaciones del egreso. La cama liberada también pasa al estado limpieza.

Una cama en limpieza solamente puede cambiar al estado disponible después de que el personal autorizado confirme su sanitización. De esta manera, el sistema evita que una cama liberada vuelva a utilizarse sin completar previamente el proceso de limpieza.

El módulo incluye validaciones de autenticación, permisos, tenant, admisión activa, disponibilidad de camas y datos obligatorios. También utiliza transacciones, auditoría, notificaciones y controles de concurrencia para prevenir operaciones parciales o inconsistentes.

## 3. Requisitos, reglas de negocio y excepciones

El análisis del módulo permitió definir quince requisitos funcionales, diez reglas de negocio y once excepciones. Estos elementos sirven como base para los tres diagramas UML y para la matriz de trazabilidad.

### 3.1 Requisitos funcionales

- RF-01: seleccionar una admisión activa del tenant actual.
- RF-02: elegir entre traslado interno y alta hospitalaria.
- RF-03: validar identidad, rol y permisos del usuario.
- RF-04: validar que la admisión, el paciente y las camas pertenezcan al mismo tenant.
- RF-05: validar que la cama destino sea distinta de la cama actual.
- RF-06: validar que la cama destino se encuentre disponible.
- RF-07: asociar la admisión con la cama destino durante un traslado exitoso.
- RF-08: cambiar la cama destino al estado ocupada.
- RF-09: cambiar la cama liberada al estado limpieza.
- RF-10: cerrar la admisión y registrar los datos del egreso durante un alta.
- RF-11: cambiar una cama de limpieza a disponible únicamente después de confirmar su sanitización.
- RF-12: registrar en auditoría las operaciones exitosas y rechazadas.
- RF-13: devolver una respuesta comprensible con el resultado.
- RF-14: notificar a los responsables cuando la operación finalice o requiera atención.
- RF-15: ejecutar como una sola transacción las actualizaciones principales.

### 3.2 Reglas de negocio

- Solo puede procesarse una admisión activa.
- Cada usuario puede operar únicamente dentro de su tenant.
- La cama destino no puede ser la misma cama actual.
- Una cama ocupada, en limpieza o en mantenimiento no puede utilizarse como destino.
- El traslado mantiene activa la admisión.
- El alta cierra la admisión.
- Una cama liberada no pasa directamente de ocupada a disponible.
- Solo el personal autorizado puede confirmar la sanitización.
- Si falla una actualización principal, todos los cambios deben revertirse.
- Los diagramas y evidencias deben utilizar únicamente datos ficticios.

### 3.3 Excepciones principales

El proceso contempla fallos de autenticación, falta de permisos, admisiones inexistentes o inactivas, aislamiento incorrecto por tenant, selección de la misma cama, indisponibilidad de la cama destino, datos incompletos del alta, sanitización no autorizada, conflictos concurrentes y errores transaccionales.

Cuando una validación falla, la operación se rechaza sin modificar la admisión ni las camas y el intento queda registrado en auditoría. Cuando falla una actualización dentro de la transacción, todos los cambios se revierten.

Si la operación principal ya fue confirmada y posteriormente falla la notificación, los cambios no se revierten. El fallo se registra y se programa un reintento del envío.

## 4. Diagrama de casos de uso

El diagrama de casos de uso representa las funciones principales del módulo ASII-08 y delimita la interacción entre los actores humanos y los servicios de apoyo.

Los actores principales son el médico, la enfermera, el administrador hospitalario y el personal de sanitización. También participan los sistemas de autenticación y autorización, auditoría y notificaciones.

Los casos de uso centrales son gestionar un traslado interno, registrar un alta hospitalaria y confirmar la sanitización de una cama. Estas operaciones incluyen validaciones de identidad, permisos, tenant, admisión activa y estado de las camas.

El médico participa principalmente en el registro del alta hospitalaria. La enfermera interviene en la gestión del traslado interno. El personal de sanitización confirma que una cama en limpieza puede volver al estado disponible. El administrador hospitalario consulta y supervisa operaciones y situaciones excepcionales.

El sistema de autenticación y autorización valida la identidad, el rol y los permisos. El sistema de auditoría conserva evidencia de operaciones exitosas, rechazadas o fallidas. El sistema de notificaciones informa los resultados a los responsables.

La separación entre la liberación de la cama y la confirmación de sanitización permite representar la regla que impide que una cama pase directamente de ocupada a disponible.

La fuente editable del diagrama se encuentra en `diagramas/casos-de-uso.puml`. También se generaron las versiones `diagramas/casos-de-uso.png` y `diagramas/casos-de-uso.svg`.

## 5. Diagrama de actividad

El diagrama de actividad representa el flujo completo del proceso y muestra las decisiones, validaciones, excepciones y resultados posibles.

El flujo comienza con la autenticación del usuario y la validación de sus permisos. Luego se comprueba que la admisión esté activa y que pertenezca al tenant correspondiente.

En un traslado interno, el sistema valida que la cama destino sea diferente de la cama actual, que pertenezca al mismo tenant y que se encuentre disponible. Después inicia una transacción, revalida la disponibilidad para controlar solicitudes concurrentes, asigna la cama destino a la admisión, la marca como ocupada y cambia la cama origen al estado limpieza.

En un alta hospitalaria, se validan los datos obligatorios del egreso. Luego se cierra la admisión, se registran la fecha, el tipo y las observaciones, y la cama liberada pasa al estado limpieza.

En ambos casos, las actualizaciones principales y el registro de auditoría deben completarse correctamente antes de confirmar la transacción. Si ocurre un error, los cambios se revierten para mantener el estado anterior.

La sanitización se representa como un proceso posterior. El personal autorizado confirma la limpieza y el sistema cambia la cama de limpieza a disponible.

La fuente editable se encuentra en `diagramas/actividad.puml`. Las versiones generadas están disponibles en `diagramas/actividad.png` y `diagramas/actividad.svg`.

## 6. Diagrama de secuencia

El diagrama de secuencia representa el orden temporal de las interacciones entre el usuario, la interfaz, el servicio del módulo ASII-08 y los sistemas de apoyo.

Los participantes principales son el usuario autorizado, la interfaz, el servicio ASII-08, el sistema de autenticación y autorización, la base de datos hospitalaria, el sistema de auditoría, el sistema de notificaciones y el personal de sanitización.

La secuencia comienza con la validación del token, el rol, los permisos y el tenant. Después se consulta la admisión y se comprueba que se encuentre activa.

En el traslado, el sistema valida la cama destino, inicia la transacción y revalida su disponibilidad antes de actualizar la admisión y los estados de las camas. Si ocurre un conflicto concurrente o un error técnico, la transacción se revierte.

En el alta, se validan los datos del egreso y luego se cierra la admisión, se cambia la cama al estado limpieza y se registra auditoría dentro de la transacción.

La confirmación de sanitización ocurre posteriormente. El personal autorizado solicita el cambio de la cama desde limpieza hacia disponible y el sistema registra la operación.

Las notificaciones se envían después de confirmar la operación principal. Si una notificación falla, el sistema registra el fallo en auditoría, programa un reintento y conserva los cambios ya confirmados.

La fuente editable se encuentra en `diagramas/secuencia.puml`. Las versiones generadas están disponibles en `diagramas/secuencia.png` y `diagramas/secuencia.svg`.

## 7. Matriz de trazabilidad

La matriz de trazabilidad relaciona cada requisito funcional, regla de negocio y excepción con los elementos específicos de los diagramas de casos de uso, actividad y secuencia.

Esta relación permite comprobar que las necesidades definidas durante el análisis no queden representadas únicamente de forma textual, sino que aparezcan de manera verificable dentro de los modelos UML.

Los quince requisitos funcionales cuentan con cobertura completa. Entre ellos se encuentran la validación de la admisión activa, la selección del tipo de operación, el control de permisos y tenant, la validación de la cama destino, el cierre de la admisión, la liberación de la cama, la auditoría, las notificaciones y el uso de transacciones.

Las diez reglas de negocio también se encuentran representadas. Se verifica especialmente que un traslado no cierre la admisión, que un alta sí la cierre, que una cama liberada pase primero al estado limpieza y que solamente el personal autorizado pueda confirmar la sanitización.

Las once excepciones fueron trazadas hasta elementos concretos de los diagramas. Estas incluyen errores de autenticación, autorización, tenant, admisión, disponibilidad, datos incompletos, concurrencia, fallos transaccionales y fallos de notificación.

Durante la revisión se identificó inicialmente una cobertura parcial en el manejo del fallo de notificación. El diagrama de secuencia fue actualizado para registrar el fallo, conservar la operación principal confirmada y programar un reintento. Después de esta corrección, la matriz no presenta brechas pendientes.

La matriz completa y editable se encuentra en `trazabilidad/matriz-trazabilidad.md`.

## 8. Uso de inteligencia artificial

Durante el desarrollo del trabajo se utilizó ChatGPT, herramienta de inteligencia artificial de OpenAI, como apoyo técnico y metodológico.

La herramienta se empleó para organizar los requisitos de la asignación, proponer estructuras PlantUML, revisar la coherencia entre los diagramas, construir la matriz de trazabilidad y preparar comandos de PowerShell y Git.

Los resultados fueron revisados y validados por el estudiante. Se verificaron los archivos editables, se generaron las versiones PNG y SVG, se revisó visualmente cada diagrama y se comparó su contenido con los requisitos y excepciones definidos.

También se realizaron ajustes humanos, como la selección del proceso específico, la revisión de actores y participantes, el uso exclusivo de identificadores ficticios, la simplificación del diagrama de secuencia y la inclusión del manejo de fallos de notificación.

La herramienta de inteligencia artificial no ejecutó directamente comandos en el equipo local ni realizó commits o publicaciones. Estas acciones fueron ejecutadas y verificadas por el estudiante.

La declaración completa y transparente del uso de inteligencia artificial se encuentra en el archivo `DECLARACION_IA.md`.

## 9. Evidencias de Git y GitHub

El trabajo se desarrolló en un repositorio personal de GitHub y en una rama específica para la asignación.

- Repositorio: `https://github.com/00AbiMendoza/uml-asii-08-traslados-altas-camas`
- Rama de trabajo: `feature/diagramas-uml-asii-08`

El historial presenta commits separados y descriptivos para la definición de requisitos, los tres diagramas UML, la matriz de trazabilidad, la declaración de inteligencia artificial, las evidencias y la guía de defensa.

Los archivos de evidencia se encuentran en:

- `evidencia/historial-git.txt`: historial resumido de commits.
- `evidencia/estructura-repositorio.txt`: estructura de carpetas y archivos.
- `evidencia/referencia-git.md`: enlaces del repositorio, rama y commit.

Las fuentes editables de los diagramas tienen extensión `.puml`. Cada diagrama también cuenta con exportaciones en PNG y SVG, lo que permite comprobar tanto su edición como su representación visual.

Antes de cada commit se revisó el estado del repositorio mediante `git status`. Después de registrar los cambios, estos fueron publicados en GitHub mediante `git push`.

El commit y la etiqueta correspondientes a la versión evaluada se actualizarán después de finalizar y validar el documento completo.

## 10. Conclusión

El trabajo permitió modelar de manera coherente el proceso de traslados, altas y liberación de camas del módulo ASII-08 mediante diagramas de casos de uso, actividad y secuencia.

La principal decisión de diseño fue separar la liberación de la cama de su disponibilidad final. Después de un traslado o alta, la cama pasa al estado limpieza y solamente puede volver a estar disponible cuando el personal autorizado confirma su sanitización.

También se incorporaron validaciones de autenticación, permisos, tenant, admisión activa, disponibilidad, concurrencia y transacciones. Esto permite representar cómo el sistema debe evitar operaciones parciales o inconsistentes.

La matriz de trazabilidad confirmó que los requisitos funcionales, las reglas de negocio y las excepciones definidas cuentan con representación dentro de los diagramas. La principal corrección realizada fue completar el manejo de fallos de notificación sin revertir una operación principal ya confirmada.

Como limitación, los diagramas representan el comportamiento esperado a nivel de análisis y diseño, pero no constituyen una implementación ejecutable del sistema. Su validez técnica deberá complementarse posteriormente con el desarrollo, las pruebas y la integración del módulo.

La evidencia del cumplimiento se conserva mediante los archivos editables PlantUML, las exportaciones PNG y SVG, la matriz de trazabilidad, el historial de Git, la referencia del repositorio y la declaración del uso de inteligencia artificial.

## 11. Bibliografía

Git. (s. f.). *Git documentation*. https://git-scm.com/docs/git

Object Management Group. (2017). *OMG Unified Modeling Language (OMG UML), versión 2.5.1*. https://www.omg.org/spec/UML/2.5.1

OpenAI. (2022, 30 de noviembre). *Introducing ChatGPT*. https://openai.com/index/chatgpt/

PlantUML. (s. f.). *PlantUML: Open-source tool that uses simple textual descriptions to draw UML diagrams*. https://plantuml.com/

Universidad Mariano Gálvez de Guatemala. (2026). *Lineamientos de la asignación Diagramas UML por módulo* [Material del curso Análisis de Sistemas II].
