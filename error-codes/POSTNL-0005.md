---
title: "Gescheiden adresregels"
code: POSTNL-0005
---

### Probleem

Op dit moment wordt er in de shop geen gebruik gemaakt van de gescheiden adres regels. Dit kan problemen veroorzaken bij het printen van verzend-labels. Bij internationale adressen is het zelfs noodzakelijk om gescheiden adres regels te hebben.

### Oplossing

Magento Community

a.) Ga in uw Magento beheeromgeving naar _Systeem > Configuratie_ en klik links onder het kopjeÂ 

Â  Â  Â  Â _Klanten_ op _Klant Configuratie._

b.) Klik vervolgens op het kopje _Naam en Adresinstellingen_.

c.) Voer bij het veld _Aantal regels in adres_ het getal _3_ in en klik op de _Sla Configuratie op_ button.

![]({{site.baseurl}}/assets/images/POSTNL-0005_0.png)

Ga verder bij stap d.

Magento Enterprise  
Volg deze a,b,c stappen alleen wanneer u een Magento Enterprise webshop heeft.

a.) Ga in uw Magento beheeromgeving naar _Klanten > Attributen > Beheer klant adresgegevens._

b.) Klik op de attribuutcode _street._

c.) Voer bij het veld _lijn telling het_ getal _3_ in en klik op de _sla eigenschap op button._

![]({{site.baseurl}}/assets/images/POSTNL-0005_1.png)Â 

  

Â 

Vervolgstappen voor Magento Community en Magento Enterprise

d.) Ga weer _naar Systeem > Configuratie_ en klik op _PostNL_. Klik op het kopje Winkel Instellingen.

e.) Zet een vinkje achter 'Deel straatnaam velden op'_._

![]({{site.baseurl}}/assets/images/POSTNL-0005_2.png)Â  Â  Â  Â  Â  Â  Â  Â  Â  Â 

f.) Kies bij _Straatnaam veld_ de gewenste adresregel_._

g.) Kies bij _Huisnummer veld_ de gewenste adresregel.

h.) Zet een vinkje achter 'Deel huisnummer op'_._

i.) Kies bij _Huisnummer extensie veld_ de gewenste adresregel en klik op de _Sla configuratie opÂ _button.

**Let op:** Zorg dat voor straatnaam, huisnummer en huisnummer extensie verschillendeÂ velden worden gebruikt.

j.)Â Â  Eventueel kunt u voor _Gebied, Gebouw naam, Afdeling naam, Deurcode_ en _Verdieping_ nog aparteÂ adresvelden aanmaken. Hiervoor moet u het getal dat u bij stap _c_ heeft ingevoerd aanpassen.Â 

**Let op:** Voor Magento Community kunnen er maximaal 4 adresregels worden ingesteld. Â  Â  Â  Â  Â  Â  Â Â 

De adresgegevens zijn nu juist ingesteld. Vergeet niet de velden in de frontend goed te labelen. Hoe dit moet kun je [hier](https://confluence.tig.nl/hc/nl/articles/210515658) vinden.
