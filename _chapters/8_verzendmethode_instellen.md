## Verzendmethode instellen
Onderdeel van de PostNL extensie is de PostNL verzendmethode waarin de verzendkosten bepaald kunnen worden. De basis van de verzendmethode is vergelijkbaar met andere Magento verzendmethodes met daarin de opties om de verzendmethode te activeren, tarieven, verzendlanden en de titel in te stellen.
Voor het instellen van de tarieven zijn er binnen de PostNl verzendmethode drie opties: <em>vast tarief</em>, <em>table rates</em> en de <em>matrix rates</em>.
### Vast tarief
Bij de optie vast tarief kan er één tarief ingesteld worden wat gehanteerd als verzendkosten naar alle landen die geactiveerd staan.
In de instellingen van de verzendmethoden is het tevens mogelijk een aparte prijs te hanteren voor zendingen die worden opgehaald bij een PostNL afhaalpunt. Gebruik hiervoor de instellingen "Other price for Pickup" en "Price for pickup"

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
Met behulp van de matrix rates kunnen de verzendkosten bepaald worden op basis van een combinatie van de bestemming, het gewicht, aantal artikelen, de prijs en het pakkettype, wat veel flexibiliteit geeft in het instellen van de verzenkosten.
Wanneer het `Rate type` ingesteld staat op Matrix, dan verschijnt de optie om de Matrix rate te importeren of om de huidige versie te downloaden

![PostNL Matrix Rates]({{ site.baseurl }}/assets/images/8_verzendmethode_matrix_csv.png "PostNL Magento 2 Matrix rates")

> **BELANGRIJK:** Zet altijd de regel voor het hoogste bedrag/gewicht bovenaan. Het systeem kijkt namelijk eerst naar de bovenste regel en kijkt daarna naar de daarop volgende regel.
Als de regel voor verzendkosten bij een bedrag van 0 of hoger boven de regel staat van 100 of hoger, dan zal de regel van 0 of hoger altijd als eerste gebruikt worden omdat deze bovenaan staat.

De eerste rij in de csv bevat altijd de namen van de kolommen die gebruikt worden voor de bepaling van de verzendkosten.
- **Land:** De landcode waarvoor de verzendkosten gelden.
- **Provincie/staat:** De provincie/staat binnen een land waarvoor de verzendkosten gelden.
- **Postcode:** De postcode waarvoor de verzendkosten gelden.
- **Gewicht (en hoger):** De verzendkosten bepalen op basis van een bepaald gewicht.
- **Bedrag (en hoger):** De verzendkosten bepalen op basis van een bepaald orderbedrag.
- **Hoeveelheid (en hoger):** De verzendkosten bepalen op basis van het aantal artikelen in een order.
- **Pakkettype:** Het pakkettype waarvoor de verzendkosten gelden (pakjegemak, regular).
- **Prijs:** De verzendkosten die gerekend worden zodra de order aan de bepaalde instellingen voldoet.
- **Instructies:** Instructies voor intern gebruik.

Zie ook deze [voorbeeld csv]({{ site.baseurl }}/assets/images/matrix-rates-postnl.csv).

#### Visuele matrix
Naast de optie om de matrix middels een csv te uploaden is er ook de optie om dit in te voeren middels de visuele matrix. Er zijn twee manieren om naar de visuele matrix te gaan:
1. Klik in de backend in het linkermenu op PostNL en klik vervolgens op matrix rates
2. Ga in de backend naar Systeem > Configuratie > Verzendmethodes > PostNL, zet de Rate Type op Matrix en klik op “Toon visuele matrix”.

![PostNL Matrix Rates Visueel]({{ site.baseurl }}/assets/images/8_verzendmethode_matrix_visueel.png "PostNL Magento 2 Matrix rates visueel")

Hierin ziet u de huidige matrix rates die al eerder ingesteld zijn, daarnaast kunt u hier ook nieuwe matrix rates toevoegen. Klik hiervoor rechtsboven op “Voeg een nieuwe matrix toe”.
![PostNL Matrix Rates Grid]({{ site.baseurl }}/assets/images/8_verzendmethode_matrix_grid.png "PostNL Magento 2 Matrix rates grid")

Vervolgens vult u de gegevens in:
![PostNL Matrix Rates Add]({{ site.baseurl }}/assets/images/8_verzendmethode_matrix_add.png "PostNL Magento 2 Matrix rates add")

- **Website:** De website Scope waar de desbetreffende matrix voor zal gelden
- **Land:** Selecteer de landen waarvoor dit tarief geldt. Houdt “ctrl” ingedrukt en klik op de gewenste landen.
- **Postcode:** De postcode waarvoor het verzendtarief geldt. Vul een '*' als dit tarief geldt voor alle postcodes. Geldt dit tarief niet voor alle postcodes? Voer dan maximaal 1 postcode per keer in.
- **Minimale gewicht (en hoger):** Dit verzendtarief geldt voor bestellingen van minimaal dit gewicht. Let op, gebruik '.' (PUNT) i.p.v. ',' (KOMMA).
- **Minimaleedrag (en hoger):** Dit verzendtarief geldt voor bestellingen van minimaal dit bedrag. Gebruik een ‘.’ (PUNT) in plaats van een ‘,’ (KOMMA).
- **Minimale bestel hoeveelheid (en hoger):** Dit verzendtarief geldt vanaf minimaal deze hoeveelheid producten.
- **Pakkettype:** Alle pakket types, Regulier, Afhalen PostNL punt, Brievenbuspakje of Extra@Home.
- **Prijs:** De verzendkosten die gerekend worden zodra de order aan bovenstaande instellingen voldoet.

Bij `Land` kunt u meerdere landen selecteren. Na het opslaan zal elk land een eigen regel
krijgen. Mocht u een regel willen wijzigen dan zult u eerst de regel moeten verwijderen en
dan weer opnieuw moeten toevoegen.
