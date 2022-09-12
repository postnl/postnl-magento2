## Extra instellingen

### Printerinstellingen
Onder de printerinstellingen kan het formaat van de labels ingesteld worden, waarbij er gekozen kan worden tussen A4 en A6. 
Daarnaast kan er gekozen worden om de pdf direct te downloaden of om deze in een nieuw venster te openen.
![Logging]({{ site.baseurl }}/assets/images/5_extra_printer.png "PostNL Magento 2 printerinstellingen")

### Label- en packingslipopties
Binnen de PostNL extensie is er de opties om labels te printen of of pakbonnen te printen. Voor beide opties zijn er configuraties beschikbaar.

Op de labels verschijnt linksboven een referentie, hierbij kan er gekozen worden om hiervoor geen waarde, zending ID, bestelling ID of een eigen waarde weer te geven. 
Bij deze laatste optie kan bijvoorbeeld een voorvoegsel toegevoegd worden aan het zending ID. 
Hiervoor kunnen de volgende variabelen gebruikt worden: `{{var shipment_increment_id}}`, `{{var order_increment_id}}` en `{{var store_frontend_name}}`. 
De uiteindelijke tekst mag niet meer zijn dan 28 karakters.

Standaard wordt het aantal pakketten berekend op het gewicht, waarbij 20kg als maximaal gewicht wordt genomen. 
Bij bestellingen die boven dit gewicht uitkomen, zal er voor ieder pakket een los label worden geprint. 
Er kan ook worden gekozen om het op basis van een vooraf gedefinieerd aantal pakketten te doen.

![Logging]({{ site.baseurl }}/assets/images/5_extra_label_packingslip.png "PostNL Magento 2 label en pakbon instellingen")

#### Barcode-instellingen
Op de pakbonnen kan een barcode geprint worden, na het scannen zal deze de bijpassende order erbij zoeken in het order grid. 
Standaard wordt de barcode rechtsboven op de pakbon geprint, deze positie kan naar wens aangepast worden op basis van coördinaten op de PDF.

Voor de barcode kan er gekozen worden uit diverse type barcodes te weten *Code 25*, *Code 39*, *Code 128*, *RoyalMail*.
![Logging]({{ site.baseurl }}/assets/images/5_extra_barcode_pakbon.png "PostNL Magento 2 barcode pakbon")

### Adresvalidatie
Door de adresvalidatie te activeren wordt er een adresvalidatie toegevoegd in de frontend van de webshop voor Nederlandse adressen.
![Logging]({{ site.baseurl }}/assets/images/5_extra_adresvalidatie_front.png "PostNL Magento 2 adresvalidatie checkout")
Bij de adresvalidatie wordt op basis van de ingevoerde postcode en huisnummer, automatisch de straatnaam en de plaatsnaam ingeladen.
De adresvalidatie voorkomt daarmee fouten in de ingevoerde Nederlandse adressen.

Binnen de instellingen kan worden aangegeven of het design van de webshop is gebaseerd op het Magento Blank of het Magento Luma thema, om zo de best passende styling te verzorgen.
![Logging]({{ site.baseurl }}/assets/images/5_extra_adresvalidatie.png "PostNL Magento 2 adresvalidatie")


### Geavanceerde instellingen
Onder de geavanceerde instellingen kan worden aangegeven welke verzendmethoden gebruikt worden voor PostNL zendingen. De bestellingen die middels deze verzendmethoden worden aangemaakt, worden verwerkt door de PostNL extensie.
![Logging]({{ site.baseurl }}/assets/images/5_extra_geavanceerd.png "PostNL Magento 2 geavanceerde instellingen")
Door de toolbar te activeren komen er extra opties in het bestellingen en verzendingen overzicht, waarmee onder andere de productcode of het aantal pakketten van een order aangepast kan worden.
![Logging]({{ site.baseurl }}/assets/images/5_extra_geavanceerd_toolbar.png "PostNL Magento 2 geavanceerde instellingen")
