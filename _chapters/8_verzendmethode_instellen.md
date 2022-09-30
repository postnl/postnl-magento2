## Verzendmethode instellen
Onderdeel van de PostNL extensie is de PostNL verzendmethode waarin de verzendkosten bepaald kunnen worden. De basis van de verzendmethode is vergelijkbaar met andere Magento verzendmethodes met daarin de opties om de verzendmethode te activeren, tarieven, verzendlanden en de titel in te stellen.
Voor het instellen van de tarieven zijn er binnen de PostNl verzendmethode drie opties: <em>vast tarief</em>, <em>table rates</em> en de <em>matrix rates</em>.
### Vast tarief
Bij de optie vast tarief kan er één tarief ingesteld worden wat gehanteerd als verzendkosten naar alle landen die geactiveerd staan.

### Table rates
De table rates verzendmethode komt overeen met de gelijknamige verzendmethode van Magento: [https://docs.magento.com/user-guide/shipping/shipping-table-rate.html](https://docs.magento.com/user-guide/shipping/shipping-table-rate.html).

Bij de table rates kunnen de verzendkosten bepaald worden op basis van twee variabele en daarin zijn de volgende 3 combinaties mogelijk:
- Prijs versus bestemming
- Gewicht versus bestemming
- Aantal artikelen versus bestemming

De tarieven kunnen vervolgens met behulp van een csv geupload worden. Voor het uploaden van de csv dient er geswitched te worden naar de website scope. Deze optie staat links boven in de configuratie van Magento:
![PostNL Table Rates]({{ site.baseurl }}/assets/images/8_verzendmethode_table.png "PostNL Magento 2 table rates")

#### Prijs versus bestemming
Bij deze optie wordt het verzendtarief bepaald op basis van het subtotaal en de verzendlocatie. Op deze manier er per land een ander tarief worden gehanteerd en boven een bepaald bedrag is het mogelijk om bijvoorbeeld het verzenden gratis te maken. 
Zie ook deze [voorbeeld csv]({{ site.baseurl }}/assets/images/tablerates-postnl-prijs.csv).

#### Gewicht versus bestemming
Bij deze optie wordt de verzendtarief bepaald op basis van het gewicht en de verzendlocatie. Op deze manier er per land een ander tarief worden gehanteerd en boven een bepaald gewicht is het mogelijk om een hoger tarief in rekening te brengen.
Zie ook deze [voorbeeld csv]({{ site.baseurl }}/assets/images/tablerates-postnl-gewicht.csv).

#### Aantal artikelen versus bestemming
Bij deze optie wordt de verzendtarief bepaald op basis van het aantal artikelen en de verzendlocatie. Op deze manier er per land een ander tarief worden gehanteerd en boven een bepaald aantal artikelen is het mogelijk om een hoger tarief in rekening te brengen.
Zie ook deze [voorbeeld csv]({{ site.baseurl }}/assets/images/tablerates-postnl-aantal.csv).

### Matrix rates