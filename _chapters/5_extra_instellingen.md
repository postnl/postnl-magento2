## Extra instellingen

### Printerinstellingenn
Onder de printerinstellingen kan het formaat van de labels ingesteld worden, waarbij er gekozen kan worden tussen A4 (normale printer) en A6 (labelprinter). Ook kan er gekozen worden om de pdf direct te downloaden of om deze in een nieuw venster te openen. Daarnaast kan er in het menu “Labeltype” aangegeven worden met welk bestandstype de labels aangemaakt worden. 
Voor een hoge printkwaliteit is het belangrijk om het bestandstype te selecteren wat overeenkomt met het standaard bestandstype van de printer waar de labels mee afgedrukt worden..

![image](https://github.com/postnl/postnl-magento2/assets/31507888/d16219f8-5e8a-4538-9c91-2886f7005e17)

![Printerinstellingen]({{ site.baseurl }}/assets/images/5_extra_printer.png "PostNL Magento 2 printerinstellingen")

### Label- en packingslip opties
Binnen de PostNL extensie is er de opties om labels te printen of of pakbonnen te printen. Voor beide opties zijn er configuraties beschikbaar.

Op de labels verschijnt linksboven een referentie, hierbij kan er gekozen worden om hiervoor geen waarde, zending ID, bestelling ID of een eigen waarde weer te geven. 
Bij deze laatste optie kan bijvoorbeeld een voorvoegsel toegevoegd worden aan het zending ID. 
Hiervoor kunnen de volgende variabelen gebruikt worden: {% raw %}`{{var shipment_increment_id}}`, `{{var order_increment_id}}` en `{{var store_frontend_name}}`{% endraw %}. 
De uiteindelijke tekst mag niet meer zijn dan 28 karakters.

Standaard wordt het aantal pakketten berekend op het gewicht, waarbij 20kg als maximaal gewicht wordt genomen. 
Bij bestellingen die boven dit gewicht uitkomen, zal er voor ieder pakket een los label worden geprint. 
Er kan ook worden gekozen om het op basis van een vooraf gedefinieerd aantal pakketten te doen.

![Label en pakbon instellingen]({{ site.baseurl }}/assets/images/5_extra_label_packingslip.png "PostNL Magento 2 label en pakbon instellingen")

#### Barcode-instellingen
Op de pakbonnen kan een barcode geprint worden, na het scannen zal deze de bijpassende order erbij zoeken in het order grid. 
Standaard wordt de barcode rechtsboven op de pakbon geprint, deze positie kan naar wens aangepast worden op basis van coördinaten op de PDF.

Voor de barcode kan er gekozen worden uit diverse type barcodes te weten *Code 25*, *Code 39*, *Code 128*, *Royal Mail*.
![Barcode pakbon]({{ site.baseurl }}/assets/images/5_extra_barcode_pakbon.png "PostNL Magento 2 barcode pakbon")

### Adresvalidatie
De adresvalidatie is opgesplitst in de postcode check (Nederlandse adresvalidatie) en de internationale adresvalidatie.

#### Postcode check
Bij de Nederlandse adresvalidatie wordt op basis van de ingevoerde postcode en huisnummer, automatisch de straatnaam en de plaatsnaam ingeladen.

![Nederlandse adresvalidatie in de frontend]({{ site.baseurl }}/assets/images/5_extra_adresvalidatie_front.png "PostNL Magento 2 adresvalidatie checkout")

Binnen de instellingen kan worden aangegeven of het design van de webshop is gebaseerd op het Magento Blank of het Magento Luma thema, om zo de best passende styling te verzorgen.
![Instellingen Nederlandse adresvalidatie]({{ site.baseurl }}/assets/images/5_extra_adresvalidatie.png "PostNL Magento 2 adresvalidatie")

#### Internationale adresvalidatie
De internationale adresvalidatie kan geactiveerd worden door de optie `Internationale Adresvalidatie Inschakelen` op `Ja` te zetten. Om gebruik te kunnen maken van deze service dient uw API-key geldig te zijn voor deze dienst, nadat de configuratie is opgeslagen kan dit eenvoudig gevalideerd worden.

![Internationale adresvalidatie]({{ site.baseurl }}/assets/images/5_extra_internationale_adresvalidatie.png "PostNL Magento 2 adresvalidatie")

Is de API-Key niet geldig voor Internationale Adres Validatie? Stuur een verzoek om uw API-key geldig te maken voor deze dienst naar [DataSolutions@postnl.nl](mailto:DataSolutions@postnl.nl) met de volgende gegevens:
- Voornaam:
- Achternaam:
- Bedrijfsnaam:
- KvK-nummer:
- Email adres:
- PostNL Klantnummer:
- Opmerking: (geef hier aan dat u de Internationale Adres Validatie wilt gebruiken in Magento 2)

> **Let op:** deze dienst kan extra kosten met zich meebrengen als u veel Internationale adressen valideert.

In de checkout van de webshop zal er onderin een kopje verschijnen met `adres validatie` waarbij wordt aangegeven of het adres correct is of niet. Mocht het adres niet valide zijn, dan worden er suggesties gegeven voor het correcte adres.
![Internationale adresvalidatie front]({{ site.baseurl }}/assets/images/5_extra_internationale_adresvalidatie_front.png "PostNL Magento 2 adresvalidatie front")




### Geavanceerde instellingen
Onder de geavanceerde instellingen kan worden aangegeven welke verzendmethoden gebruikt worden voor PostNL zendingen. De bestellingen die middels deze verzendmethoden worden aangemaakt, worden verwerkt door de PostNL extensie.
![Logging]({{ site.baseurl }}/assets/images/5_extra_geavanceerd.png "PostNL Magento 2 geavanceerde instellingen")
Door de toolbar te activeren komen er extra opties in het bestellingen en verzendingen overzicht, waarmee onder andere de productcode of het aantal pakketten van een order aangepast kan worden.
![Logging]({{ site.baseurl }}/assets/images/5_extra_geavanceerd_toolbar.png "PostNL Magento 2 geavanceerde instellingen")
