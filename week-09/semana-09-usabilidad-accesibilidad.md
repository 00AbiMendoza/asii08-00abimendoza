# Semana 9 — Evaluación del diseño, usabilidad y accesibilidad

## Módulo ASII-08: Traslados, altas y liberación de camas

## 1. Propósito

Evaluar el flujo UX de `semana-08-diseno-ux.md` (user flow + 6 wireframes) con heurísticas de usabilidad y criterios WCAG — teclado, foco, contraste, etiquetas, mensajes y prevención de errores — y priorizar correcciones según su impacto real en traslados, altas, ocupación y liberación atómica de camas.

**Nota metodológica:** los wireframes evaluados son de baja fidelidad (PlantUML `salt`), no una interfaz Vue implementada. Por eso esta evaluación distingue explícitamente entre **defectos verificables ahora** (ej. contraste de un color ya elegido) y **decisiones de interacción todavía no especificadas** (ej. si un tooltip responde a teclado) — ambas son hallazgos legítimos, pero no se afirma como "falla" algo que en realidad es "no definido todavía".

## 2. Checklist de heurísticas

| Heurística | W1 Inicio | W2 Decisión | W3 Traslado (vacío) | W4 Carga/éxito | W5 Error | W6 Alta |
|---|---|---|---|---|---|---|
| **Teclado** — toda acción operable sin mouse | ⚠ no especificado | ⚠ no especificado | ✓ campos estándar | — | ✓ botones estándar | ⚠ tooltip (?) sin definir |
| **Foco** — orden y movimiento predecible | ⚠ no especificado | ⚠ foco inicial no definido | ✓ foco al campo con error | — | ⚠ no se mueve al error | ✓ foco al campo requerido |
| **Contraste** — texto legible (mín. 4.5:1) | ✓ texto negro/gris estándar | ✓ | ✓ rojo verificado 7.33:1 | ✓ | ✓ rojo verificado 7.33:1 | ✓ |
| **Etiquetas** — asociadas a su control | ⚠ label visible, asociación `for` sin especificar | ✓ | ✓ "Cama destino *" | — | ✓ | ✓ "Tipo de alta *" |
| **Mensajes** — perceptibles por toda tecnología de asistencia | ✓ texto explicativo | ✓ | ✓ texto + color | ⚠ sin `aria-live` especificado | ✓ ícono + color + texto | ✓ |
| **Prevención de errores** — confirmación antes de acción crítica/irreversible | — | ✓ deriva a formularios | ✓ bloquea sin cama destino | — | ✓ conserva datos, permite reintentar | ⚠ sin confirmación adicional para acción irreversible |

`✓` cubierto en el wireframe · `⚠` hallazgo (gap real o no especificado) · `—` no aplica a esa pantalla

## 3. Hallazgos (7)

### H1 — Ayuda contextual solo por hover (W6)

El ícono `(?)` que explica los 4 tipos de alta no especifica un mecanismo accesible por teclado. Si se implementa solo con `:hover` de CSS, los usuarios que navegan por teclado o con lector de pantalla no podrían acceder a esa ayuda. **Heurística:** teclado (WCAG 2.1.1).

### H2 — Foco no se mueve al mensaje de error (W5)

Cuando la cama destino deja de estar disponible (CA-02) y aparece el mensaje de error, el flujo no especifica que el foco de teclado se mueva hacia él. Un usuario de lector de pantalla podría no enterarse de que el traslado falló. **Heurística:** foco (WCAG 2.4.3) + mensajes (WCAG 4.1.3).

### H3 — Paleta de color sin verificación formal como estándar

El rojo usado en los wireframes (`#B00020`) fue verificado matemáticamente contra fondo blanco: **7.33:1**, cumple AA y AAA. Sin embargo, este valor se verificó solo para este color puntual — no existe todavía una guía de estilo/paleta formal que garantice ese mismo contraste en el resto de la interfaz Vue cuando se implemente. **Heurística:** contraste (WCAG 1.4.3) — hallazgo de proceso, no de una falla ya cometida.

### H4 — Asociación programática de etiqueta sin especificar (W1)

El campo de búsqueda tiene una etiqueta visible ("Buscar admisión activa:"), pero el wireframe no especifica que esté asociada al control mediante `<label for>` o `aria-labelledby`. Sin esa asociación, un lector de pantalla podría no anunciar la etiqueta al enfocar el campo. **Heurística:** etiquetas (WCAG 1.3.1 / 4.1.2).

### H5 — Estado de carga/éxito sin región viva (W4)

El cambio de "Procesando traslado..." a "Traslado registrado correctamente" es solo visual. Sin una región `aria-live="polite"` (o equivalente), un usuario que no ve la pantalla no se entera del resultado sin volver a explorarla activamente. **Heurística:** mensajes (WCAG 4.1.3).

### H6 — Sin confirmación adicional antes de un alta (irreversible) (W6)

El alta "cierra la admisión y no puede repetirse" (RB-06), pero el flujo no incluye un paso de confirmación específico para esta acción irreversible — usa el mismo paso genérico de "Confirmación" que el traslado (que sí es reversible mediante un traslado posterior). **Heurística:** prevención de errores (WCAG 3.3.4, acciones que modifican datos de forma irreversible).

### H7 — Foco inicial no definido entre dos acciones (W2)

Al llegar a la pantalla de decisión, no se especifica cuál de los dos botones (`Trasladar paciente` / `Dar de alta`) recibe el foco por defecto — o si ninguno lo recibe hasta que el usuario presione Tab. **Heurística:** foco (WCAG 2.4.3).

## 4. Backlog priorizado

Prioridad según impacto real en la operación crítica del módulo (traslado/alta/ocupación/liberación atómica de cama) — no según facilidad de implementación.

| # | Hallazgo | Impacto | Corrección propuesta | Criterio verificable |
|---|---|---|---|---|
| 1 | H6 — Sin confirmación en alta irreversible | **Alto** — un alta accidental no puede revertirse (RB-06) | Agregar un paso de confirmación explícito tipo "¿Confirma el alta? Esta acción no se puede deshacer" antes de enviar `discharge_type` | Un usuario no puede completar un alta sin pasar por un diálogo de confirmación distinto al de traslado; verificable con prueba manual o E2E que intente saltarlo |
| 2 | H2 — Foco no se mueve al error de traslado | **Alto** — el usuario puede no enterarse de que el traslado no se ejecutó | Mover el foco de teclado al contenedor del mensaje de error al renderizarlo | Prueba de teclado: tras un `422`, `document.activeElement` debe ser el nodo del mensaje de error |
| 3 | H5 — Estado de carga/éxito sin `aria-live` | **Medio-alto** — afecta directamente si el usuario sabe si el traslado/alta se completó | Envolver el indicador de estado en un contenedor `aria-live="polite"` | Con lector de pantalla activo (NVDA/VoiceOver), el cambio de "Procesando..." a "registrado correctamente" se anuncia sin que el usuario mueva el foco |
| 4 | H1 — Ayuda contextual solo por hover | **Medio** — bloquea entender los tipos de alta a usuarios de teclado/lector | Activar el tooltip también con foco de teclado (`:focus`) y tecla Escape para cerrarlo | El contenido de ayuda es alcanzable y legible navegando solo con Tab/Enter, sin usar el mouse |
| 5 | H4 — Asociación de etiqueta sin especificar | **Medio** — afecta el campo de entrada al flujo completo | Especificar `<label for="buscar-admision">` en la guía de implementación Vue | Un lector de pantalla anuncia "Buscar admisión activa" al enfocar el campo, no un campo sin nombre |
| 6 | H3 — Paleta sin guía de estilo formal | **Medio** — riesgo de inconsistencia futura, no una falla actual | Documentar la paleta (rojo error, verde éxito, ámbar advertencia) con su contraste verificado en una guía de estilo antes de implementar Vue | Cada color de estado tiene su ratio de contraste documentado y ≥ 4.5:1 contra su fondo, antes de escribir el primer componente |
| 7 | H7 — Foco inicial no definido en decisión | **Bajo** — conveniencia, no bloquea la operación | Definir que el foco inicial caiga en el primer elemento interactivo (según DOM, no necesariamente un botón de acción) | Al cargar la pantalla de decisión, `document.activeElement` es un elemento enfocable predecible, no `body` |

## 5. Conclusión

De los 7 hallazgos, los dos de mayor impacto (confirmación de alta irreversible, foco en errores de traslado) afectan directamente la operación crítica del módulo — no son mejoras cosméticas. El resto son gaps de especificación reales pero de menor riesgo inmediato. Ningún hallazgo se basa en una suposición no verificada: el contraste se calculó matemáticamente (H3) y el resto son ausencias explícitas en el diseño ya entregado en la semana 8, no fallas inventadas para completar el mínimo pedido.
