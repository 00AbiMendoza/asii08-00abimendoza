# GUIA DE DEFENSA ORAL

## Proyecto

Micro-HIS Traslados, altas y liberacion de camas.

## 1. Que problema resuelve

El modulo permite ejecutar dos operaciones principales:

- trasladar un paciente de una cama a otra;
- dar de alta al paciente y liberar consistentemente la cama utilizada.

La liberacion no deja la cama disponible inmediatamente. Primero cambia a estado `cleaning`, porque antes de volver a utilizarse debe pasar por limpieza.

## 2. Arquitectura utilizada

El proyecto esta separado en cuatro capas:

### Presentation

Recibe los comandos desde consola y envia los datos hacia los casos de uso.

Archivo principal:

`src/Presentation/ConsoleApplication.php`

### Application

Contiene los casos de uso:

- `TransferPatient`
- `DischargePatient`

Esta capa coordina las reglas del proceso y las transacciones.

### Domain

Contiene las entidades y contratos principales:

- `Bed`
- `Admission`
- `BedRepository`
- `AdmissionRepository`

Aqui se encuentran las reglas de negocio relacionadas con camas y admisiones.

### Persistence

Implementa los repositorios utilizando PDO y SQLite.

Incluye:

- `PdoConnection`
- `PdoBedRepository`
- `PdoAdmissionRepository`
- `PdoTransactionManager`

## 3. Regla principal de dominio

Una cama destino debe estar disponible antes de realizar un traslado.

La cama de origen debe pasar de `occupied` a `cleaning`.

Una cama no pasa directamente de `occupied` a `available`.

## 4. Consistencia mediante transacciones

El traslado y el alta utilizan transacciones.

Si todas las operaciones de persistencia terminan correctamente se ejecuta `commit`.

Si ocurre un error se ejecuta `rollback`.

Esto evita que una parte de la operacion quede almacenada y otra no.

## 5. Uso de PDO

La persistencia utiliza PDO y sentencias preparadas.

Ejemplo:

`PdoBedRepository` busca y actualiza camas mediante consultas preparadas con parametros.

## 6. Pruebas automatizadas

Se implementaron cuatro pruebas:

1. traslado exitoso;
2. rechazo de traslado cuando la cama destino esta ocupada;
3. rollback ante un error de persistencia simulado;
4. alta exitosa.

Resultado obtenido:

`4 aprobadas, 0 fallidas`.

## 7. Ejemplo para demostrar en la defensa

Estado inicial:

- cama 1: occupied;
- cama 2: available;
- admision 1: activa en cama 1.

Comando:

`php micro-his.php transfer 1 2`

Resultado:

- cama 1 pasa a cleaning;
- cama 2 pasa a occupied;
- la admision sigue activa pero queda asociada a la cama 2.

## 8. Decision tecnica mas importante

La decision principal fue mantener las reglas del negocio separadas de PDO y de la entrada por consola.

Gracias a esto, los casos de uso pueden probarse utilizando dobles de prueba sin depender de una base de datos real.

## 9. Limitacion actual

El ejercicio utiliza SQLite y una interfaz de consola porque su objetivo es educativo y demostrar la arquitectura.

En un sistema hospitalario real seria necesario agregar autenticacion, autorizacion, auditoria, concurrencia y una infraestructura de base de datos adecuada para produccion.

## 10. Cambio sencillo que podria realizarse durante la defensa

Si se solicita modificar una regla, puede explicarse el metodo `Bed::releaseToCleaning()` o cambiarse una validacion de disponibilidad.

Tambien puede ejecutarse nuevamente:

`php tests/run.php`

para demostrar que las reglas siguen funcionando despues de un cambio.
