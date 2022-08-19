---
code: POSTNL-0245
title: "Warning : To successfully send orders with Extra@Home, you must fill in the product attributes name, weight and volume."
---
### Probleem

Bij het aanmaken van een zending krijgt u de melding:
```text
"Warning : To successfully send orders with Extra@Home, you must fill in the product attributes name, weight and volume."
```

### Oorzaak

Bij het aanmaken van een *Extra@Home* zending wordt het totaal volume (cm3) en gewicht berekend. Indien deze berekening 0 terug geeft kan de zending niet aangemaakt worden.

### Oplossing
Login in de backend van uw shop en navigeer vervolgens naar de producten welke als het type *Extra@Home* ingesteld zijn. Controleer onder het tabje `General` of in het Nederlands `Algemeen` of het gewicht is ingevuld. Navigeer vervolgens door naar het tabje `PostNL` en controleer (indien het product type op *Extra@Home* staat) of het veld `PostNL product volume` is ingevuld.

Indien het product uit meerdere pakketen bestaat, tel dan het aantal kubieke centimeters bij elkaar op. 

![PostNL Extra@Home settings]({{ site.baseurl }}/assets/images/POSTNL-0245.png "Magento 2 Admin - PostNL Extra@Home")
