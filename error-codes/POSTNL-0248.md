---
code: POSTNL-0248
title: Invalid %s "%s" supplied in row #%s
---
### Probleem
De aangegeven regel en kolom van het PostNL tarieven csv bestand heeft niet het juiste bedrag format.

### Oplossing
Dit is altijd de `voorwaarden kolom`, e.g. *Vanaf gewicht x*. Echter is de naam van deze kolom dynamisch, afhankelijk van je tablerate configuratie. Dit kan een van de volgende namen hebben:
- `Order Subtotal (an above)`
- `Weight (and above)`
- `# of Items (and above)`

Zorg ervoor dat deze kolom met een nummer in de US formaat gevuld is. m.a.w. een positief nummer, waarvan de decimal separator een punt is.
