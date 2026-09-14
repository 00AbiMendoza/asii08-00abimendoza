# Enunciado y reglas de negocio — ASII-08

## 1. Identificación

- **Estudiante:** Esaú Abimael de la Cruz Mendoza
- **GitHub:** `00AbiMendoza`
- **Módulo oficial:** Traslados, altas y liberación de camas
- **Proceso asignado:** «Traslado o alta del paciente con liberación consistente de cama»
- **Repositorio:** `uml-asii-08-traslados-altas-camas`
- **Rama de trabajo:** `feature/diagramas-uml-asii-08`

## 2. Enunciado del proceso

En el Hospital Demo, una institución completamente ficticia, el personal autorizado gestiona el traslado interno o el alta de un paciente que posee una admisión activa.

Antes de ejecutar la operación, el sistema valida la identidad y los permisos del usuario, el tenant al que pertenece, la existencia de la admisión activa y la consistencia de los datos. Cuando se solicita un traslado interno, también valida que la cama de destino pertenezca al mismo tenant, sea distinta de la cama actual y esté disponible.

La operación se ejecuta de forma atómica. En un traslado interno, la admisión se asocia con la cama de destino, dicha cama pasa a estado `ocupada` y la cama anterior pasa a `limpieza`. En un alta, la admisión se cierra y la cama ocupada pasa a `limpieza`.

La cama liberada no puede volver inmediatamente a estado `disponible`. Primero debe registrarse la sanitización y ser confirmada por personal autorizado. Toda operación exitosa o rechazada debe dejar evidencia de auditoría.

## 3. Datos ficticios de referencia

Los diagramas y documentos utilizarán únicamente identificadores ficticios:

- Paciente: `PAC-DEM-001`
- Admisión: `ADM-DEM-001`
- Tenant: `HOSP-DEMO-01`
- Cama actual: `CAM-A-101`
- Cama destino: `CAM-B-204`

No se utilizarán nombres, diagnósticos, expedientes, direcciones, teléfonos ni otros datos clínicos identificables de personas reales.
