# Semana 12 — Segunda evaluación parcial

## Módulo ASII-08: Traslados, altas y liberación de camas

## 1. Propósito

Defender de forma breve las decisiones de componentes (semana 7), UX/accesibilidad/movilidad (semanas 8-10) y el prototipo navegable (semana 11), mostrando un recorrido real del prototipo y trazando cada decisión de diseño a un requisito funcional/no funcional con su evidencia verificable.

## 2. La presentación

**Archivo:** [`Semana12-SegundaEvaluacion-ASII08.pptx`](Semana12-SegundaEvaluacion-ASII08.pptx) — 6 diapositivas.

No es contenido nuevo: cada diapositiva referencia y resume el trabajo ya construido y aprobado en semanas anteriores, no introduce una versión distinta del diseño.

| # | Diapositiva | Contenido |
|---|---|---|
| 1 | Portada | Módulo, tema y datos del estudiante |
| 2 | Decisiones de componentes | Repository Pattern (semana 7) — antes/después/consecuencia, atado a `ADR-001` |
| 3 | UX, accesibilidad y movilidad | Resumen de semanas 8, 9 y 10 en tres columnas — mismo flujo evaluado y adaptado, no rediseñado tres veces |
| 4 | El prototipo (recorrido navegable) | Capturas reales del prototipo de semana 11: camino feliz y error crítico CA-02, rol Enfermera |
| 5 | Matriz decisión → RF/RNF → evidencia | 5 decisiones arquitectónicas, cada una atada a su requisito (RF/RNF) y a su prueba o artefacto verificable |
| 6 | Cambio solicitado: rol "Auditor de calidad" | Ejercicio de extensibilidad — un rol nuevo de solo lectura, evaluado contra componentes, UX, accesibilidad y movilidad ya existentes |

## 3. Recorrido navegable

El recorrido en vivo se hace directamente sobre el prototipo de la semana 11 ([`../week-11/prototipo.html`](../week-11/prototipo.html) / [`../week-11/semana-11-prototipo-navegable.md`](../week-11/semana-11-prototipo-navegable.md)): lista de admisiones → formulario de traslado → confirmación (camino feliz) o banner de error con reintento sin duplicar (CA-02). La diapositiva 4 de esta presentación son capturas de ese mismo recorrido, no un mockup nuevo.

## 4. Matriz decisión → RF/RNF → evidencia

| Decisión arquitectónica | RF/RNF | Evidencia |
|---|---|---|
| Repository Pattern (Application solo conoce interfaces) | RNF-04, RNF-05 | `TransferPatientServiceTest.php` (dobles, sin BD) |
| UI oculta acciones no autorizadas por rol | RF-09 | Wireframe W2 (semana 8) + roles de `ESPECIFICACION.md` §3 |
| Formulario bloquea envío sin cama destino | RF-03 | W3 (semana 8), M3 (semana 10), prototipo (semana 11) |
| Error crítico conserva datos, reintento no duplica | RF-08 / RB-06 | CA-02, prototipo error (semana 11) |
| Layout responsive 320-430px, botones fijos al pie | Heurística de usabilidad | M1-M5 (semana 10) |

## 5. Ejercicio de cambio: rol "Auditor de calidad"

Escenario planteado: el hospital pide un rol nuevo de solo lectura, que vea el historial de traslados/altas sin poder ejecutar acciones. Se evalúa contra lo ya construido, sin rediseñar nada:

- **Componentes:** se agrega `auditor_calidad` al catálogo de roles (Spatie Permission) — no se toca `TransferPatientService` ni `DischargePatientService`.
- **UX:** la pantalla de decisión (W2, semana 8) ya oculta botones de acción según permiso; el auditor ve la información, sin botones.
- **Accesibilidad:** mismos componentes, mismo contraste ya verificado (7.33:1, semana 9), misma navegación por teclado.
- **Movilidad:** mismo layout responsive de semana 10 — el rol no cambia el breakpoint ni el orden de contenido.

Un solo punto de cambio (catálogo de roles) — consecuencia directa de haber diseñado la UI ya condicionada por permiso desde la semana 8, no un ajuste improvisado para esta defensa.

## 6. Conclusión

Esta evaluación no aporta diseño nuevo: confirma, con un recorrido navegable real y una matriz trazable a requisitos, que las decisiones tomadas en semanas 7-11 son consistentes entre sí y sostienen un cambio razonable (el rol auditor) sin retrabajo.
