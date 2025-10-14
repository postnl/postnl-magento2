## Wereldwijd verzenden

Activeer wereldwijd verzenden voor uw klanten om bestellingen met PostNL te kunnen versturen naar landen `buiten de EU`, hiervoor wordt gebruik gemaakt van het PostNL's *GlobalPack* product.

GlobalPack kan geactiveerd worden met de gegevens die ontvangen zijn vanuit PostNL.

[https://www.postnl.nl/versturen/pakket-versturen/pakket-buitenland/](https://www.postnl.nl/versturen/pakket-versturen/pakket-buitenland/)

De instellingen voor wereldwijd verzenden staan onder: *Stores → Configuration → Sales → PostNL*, de instellingen staan onder *Wereldwijd verzenden*. Wereldwijd verzenden kan hier geactiveerd worden en de standaard productoptie voor GlobalPack kan hier ingesteld worden.

![Wereldwijde verzending instellingen]({{ site.baseurl }}/assets/images/4_wereldwijd_verzenden.png "Magento 2 Admin - PostNL Wereldwijd")

### Barcode instellingen
Onder het kopje barcode instellingen moet het barcode type (bestaande uit twee letters) en de barcode range (bestaande uit 4 cijfers) ingevuld worden zoals deze door PostNL is aangeleverd. Ontbreekt deze informatie, neem dan contact op met de PostNL account manager.

![Wereldwijde verzending barcode]({{ site.baseurl }}/assets/images/4_wereldwijd_barcode.png "Magento 2 Admin - PostNL Wereldwijd Barcode")

### Douane instellingen
Bij de douane instellingen kan het licentienummer en certificaatnummer ingevuld worden (indien u daarover beschikt) en er kan een standaard verzendtype worden geselecteerd.

Onder de douane instellingen is de mogelijkheid om *productattributen en sortering* opties in te stellen.
Hier kan aangegeven worden of er gebruik gemaakt moet worden van een HS tarief, opties voor productbeschrijving, land van herkomst en op welke manier de producten gesorteerd dienen te worden.

 - Stel bij de *Douane waarde* het attribuut in wat gebruikt moet worden om de waarde van het product te bepalen.
 - Stel bij *Land van herkomst* het attribuut in dat gebruikt moet worden om het land van herkomst van het product te bepalen.
 - Stel bij *Productomschrijving* het attribuut in dat gebruikt moet worden voor de productomschrijving.  
 - Met het *Product sortering attribuut* kan aangegeven worden op basis van welke waarde de producten gesorteerd moeten worden.
 - Bij de *Product sortering richting* kan aangegeven worden of de producten op oplopende of aflopende volgorde worden gesorteerd moeten worden.
 
 Voor het instellen van het HS Tariefnummer is het aan te raden hier een nieuw attribuut voor aan te maken op productniveau en deze per product te configureren. Dit attribuut kun je vervolgens koppelen aan de HS Tariefnummer-instelling wanneer "Gebruik het HS tarief" op "Ja" staat.

![Wereldwijde verzending douane]({{ site.baseurl }}/assets/images/4_wereldwijd_douane.png "Magento 2 Admin - PostNL Wereldwijd Douane")
