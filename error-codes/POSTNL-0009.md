---
title: "PostNL-only handeling op niet-PostNL zending"
code: POSTNL-0009
---

### Probleem

In de backend is de melding "This action cannot be used on non-PostNL shipments." of "Deze actie kan niet gedaan worden op niet-PostNL zendingen."Â zichtbaar.

### Oplossing

Enkel PostNL zendingen en orders kunnen door de PostNL extensie verwerkt worden. PostNL-only acties zijn o.a.: labels printen, zendingen voormelden, Track & Trace e-mails versturen.

Wanneer deze melding verschijnt als een bestelling wel verwerkt moet worden als PostNL zending, is het sinds 1.3.1 mogelijk om een externe verzendmetode te activeren als PostNL verzendmethode. Dit kan gedaan worden door het volgende stappenplan te volgen:Â 

Controleer binnen de bestelling welke verzendmethode is gebruikt. Deze verzendmethode moet namelijk geactiveerd worden als PostNL verzendmethode.Â 

Navigeer naar de PostNL configuratie (Systeem->configuratie->PostNL).Â 

Wanneer er gebruik gemaakt wordt van versie 1.4.1, kan de instelling gevonden worden onder Geavanceerde instellingen->Technische instellingen->PostNL verzendmethodes. Activeer hier de verzendmethode welke afgehandeld moeten worden als PostNL verzendmethode.Â 

Let er wel op dat, hoewel het meeste probleemloos werkt, er geen garantie kan worden gegeven dat een externe verendmethodegebruikt kan worden als PostNL verzendmethode.Â 

![]({{site.baseurl}}/assets/images/POSTNL-0009_0.png)

Waneer er gebruik wordt gemaakt van versie 1.4.0 of oudeer, kan deze instelling gevonden worden onder Geavanceerde instellingen->PostNL verzendmethodes.Â 

![]({{site.baseurl}}/assets/images/POSTNL-0009_1.png)

NB: De bezorogpties worden getoond op de eerst gevonden PostNL verzendmethode in de checkout. Houd hier rekening mee met het instellen van de volgorde van de verzendmethoden.Â
