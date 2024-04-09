## Algemene instellingen
Nadat de PostNL extensie is geïnstalleerd is deze in de backend van de Magento webshop terug te vinden onder *Winkels → Configuratie → Sales → PostNL*.

### Verzendinstellingen
De *overkomstduur* is het aantal dagen dat de bestelling nodig heeft om bezorgd te worden nadat deze is geplaatst. In combinatie met de ingestelde *cut-off tijden* en de *verzenddagen* bepaald de extensie wanneer de klant de bestelling kan verwachten.
![Verzend instellingen]({{ site.baseurl }}/assets/images/3_verzend_instellingen.png "Magento 2 Admin - PostNL Verzenden")

### Bezorg instellingen

#### Avondbezorging
Activeer avondbezorging om klanten de optie te bieden om hun pakket 's avonds bezorgd te krijgen. De extensie toont deze optie automatisch voor afleveradressen
waar avondbezorging beschikbaar is.

Er kan een toeslag voor het gebruik van avondbezorging ingesteld worden, deze wordt opgeteld bij de verzendkosten. Deze toeslag moeten tussen de 0,00 en 2,00 EUR incl. BTW liggen. Laat dit veld leeg om geen toeslag in rekening te brengen voor het gebruik van avondbezorging.

Voor avondbezorging kan een alternatieve productoptie ingesteld worden, standaard staat deze op `Lever alleen aan opgegeven adres`.

[https://www.postnl.nl/zakelijke-oplossingen/webwinkels/bezorgopties-voor-mijn-klanten/avondbezorging/](https://www.postnl.nl/zakelijke-oplossingen/webwinkels/bezorgopties-voor-mijn-klanten/avondbezorging/)

![Avondbezorging]({{ site.baseurl }}/assets/images/3_bezorging_avondbezorging.png "PostNL Avondbezorging")

#### Zondag bezorging
Activeer zondagbezorging om klanten de optie te bieden om hun bestelling op zondag te ontvangen. Na activatie zal zondagbezorging verschijnen als bezorgdag in de checkout.

Er kan voor zondag bezorging een toeslag ingesteld worden, deze wordt opgeteld bij de verzendkosten. Laat het veld leeg om geen toeslag in rekening te brengen.

Voor zondagbezorging kan een alternatieve productoptie ingesteld worden, standaard staat deze op `Lever alleen aan opgegeven adres`.

[https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/bezorgopties/zondagbezorging/](https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/bezorgopties/zondagbezorging/)

![Zondagbezorging]({{ site.baseurl }}/assets/images/3_bezorging_zondagbezorging.png "PostNL Zondagbezorging")

#### Bezorgdagen
Activeer bezorgdagen om klanten de optie te bieden om zelf te kiezen wanneer de bestelling geleverd wordt. Het maximale aantal bezorgdagen dat kan worden weergegeven in de checkout is 14 dagen.

[https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/bezorgopties/keuze-bezorgdag/](https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/bezorgopties/keuze-bezorgdag/)

![Bezorgdagen]({{ site.baseurl }}/assets/images/3_bezorging_bezorgdagen.png "PostNL Bezorgdagen")


#### PostNL locaties
Activeer PostNL Locaties om klanten de mogelijkheid te geven om bestellingen af te halen bij een zelf gekozen PostNL-punt. 
De PostNL locaties zijn beschikbaar voor Nederland en voor België en kunnen los van elkaar geactiveerd worden. Daarnaast kan er voor beide landen een standaard productoptie ingesteld worden die gebruikt moet worden voor overeenkomstige bestellingen.

[https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/bezorgopties/pakket-ophalen-bij-een-postnl-locatie/](https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/bezorgopties/pakket-ophalen-bij-een-postnl-locatie/)

![PostNL Locaties]({{ site.baseurl }}/assets/images/3_bezorging_postnl_locaties.png "PostNL Locaties")

#### Niet bij buren leveren
Activeer deze optie om klanten de mogelijkheid te geven of de bestelling bij de buren bezorgd mag worden in het geval dat ze zelf niet thuis zijn.
Wanneer een klant in de checkout aangeeft dat pakketten niet bij de buren bezorgd mogen worden, dan kan hiervoor een toeslag ingesteld worden in de backend en wordt opgeteld bij de verzendkosten. Laat het veld leeg om geen toeslag in rekening te brengen.

Er kan een standaard productoptie ingesteld voor verzendingen die niet bij buren geleverd mogen worden, standaard staat deze op `Lever alleen aan opgegeven adres`.

![PostNL niet bij buren leveren]({{ site.baseurl }}/assets/images/3_bezorging_niet_bij_buren.png "PostNL niet bij buren leveren")

#### Voorraad instellingen
Standaard worden de bezorgopties alleen weergegeven voor producten die op voorraad zijn, maar er kan ook gekozen worden om deze altijd weer te geven ongeacht de voorraad status.

![Voorraadinstellingen]({{ site.baseurl }}/assets/images/3_bezorging_voorraadinstellingen.png "PostNL Voorraadinstellingen")

#### ID Check
Activeer de ID check om bestelling te versturen met een leeftijdscheck. 
Voor deze bestellingen zal de pakketbezorger de leeftijd van de ontvanger van het pakket controleren.

Wanneer deze optie is geactiveerd en de configuratie is opgeslagen, dan verschijnen de ID check productopties in de dropdown met product opties.

![PostNL ID Check]({{ site.baseurl }}/assets/images/3_bezorging_id_check.png "PostNL ID Check")

#### Cargo
Met PostNL Cargo is het mogelijk om palletzendingen te versturen naar verschillende landen binnen Europa. Wanneer deze optie geactiveerd wordt, dan zullen er extra productopties beschikbaar gemaakt worden. 
>**Let op:** Om gebruik te kunnen maken van PostNL Cargo dient u eerst uw PostNL Pakketten accountmanager te raadplegen.

![PostNL Cargo]({{ site.baseurl }}/assets/images/3_bezorging_cargo.png "PostNL Cargo")

#### Pakjes Tracked
Met PostNL Pakjes Tracked kunnen kleine pakketten tot 2 kilo naar het buitenland verstuurd en gevolgd worden. 
Nadat u deze instelling heeft ingeschakeld kan de productoptie geselecteerd worden bij de standaard EPS bezorging en de standaard wereldwijde bezorging.

>**Let op:** sommige Pakjes Tracked producten vereisen specifieke contractuele afspraken met PostNL. Neem contact op met uw PostNL accountmanager voor meer informatie over deze producten.

![PostNL Pakjes Tracked]({{ site.baseurl }}/assets/images/3_bezorging_pakjes_tracked.png "PostNL Pakjes Tracked")

#### EU en België
Voor bestellingen naar België en de EU kan standaard een specifieke product optie ingesteld. Om ook de EU business product opties terug te kunnen zien in de dropdown, dient de optie *Gebruik EU business* op "Ja" gezet te worden en daarna dient de configuratie opgeslagen te worden.

De standaard geselecteerde productoptie wordt automatisch als *Zending Type* ingesteld voor de overeenkomstige bestellingen. 
Bij het bestellingen overzicht zijn deze terug te vinden in de kolom *Zending Type*.

![PostNL EU en België]({{ site.baseurl }}/assets/images/3_bezorging_eu_belgie.png "PostNL EU en Belgie")

#### Extra@Home
Met PostNL Extra@Home is gericht op de bezorging van XL-producten ne smart home producten met een installatieservice. 
Voor de Extra@Home zendingen kan een standaard productoptie ingesteld worden.

Op productniveau kan er ingesteld worden welke producten verzonden moeten worden met de Extra@Home service.

>**Let op:** Om gebruik te kunnen maken van PostNL Extra@Home dient u eerst uw PostNL Pakketten accountmanager te raadplegen.

![PostNL Extra@Home]({{ site.baseurl }}/assets/images/3_bezorging_extra@home.png "PostNL Extra@Home")

#### Track and Trace
Activeer Track and Trace voor uw klanten. Wanneer u track&trace heeft geactiveerd, wordt er automatisch een Track and Trace mail voor de order gestuurd naar de klant.
Voor de Track and Trace e-mails kunnen ook een BCC e-mails opgegeven worden, deze kunnen komma gescheiden ingevoerd worden.

![PostNL Track and Trace]({{ site.baseurl }}/assets/images/3_bezorging_track_trace.png "PostNL Track and Trace")

Er kan voor de Track en Trace e-mails ook nog een specifieke template ingesteld worden. Ga hiervoor naar *Marketing → Communications → Email Templates*.
Klik op de "Add New Template" knop en selecteer onder `TIG_PostNL` de optie `Track and Trace` om er voor te zorgen dat de correcte content in uw eigen template komt te staan.

![PostNL Track and Trace template]({{ site.baseurl }}/assets/images/3_bezorging_track_trace_template.png "PostNL Track and Trace template")

Na het klikken op Load Template worden er in het veld Template Subject en Template Content automatisch de gegevens gevuld. Deze kunnen naar wens aangepast worden. 
Nadat de nieuwe template is opgeslagen zal deze ook geselecteerd kunnen worden bij de configuratie.

![PostNL Track and Trace template selectie]({{ site.baseurl }}/assets/images/3_bezorging_track_trace_template_selectie.png "PostNL Track and Trace template selectie")

#### Brievenbuspakje
Activeer brievenbuspakje om de optie te hebben om kleine artikelen te versturen in een brievenbuspakje die eenvoudig door de brievenbus past
[https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/verzendopties/brievenbuspakje/](https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/verzendopties/brievenbuspakje/)

Binnen de extensie zijn er twee opties om gebruik te maken van brievenbuspakjes: 
- **Automatisch:** De extensie bepaalt automatisch of een bestelling als brievenbuspakje wordt verzonden.
- **Handmatig:** U bepaalt in de backend handmatig of een bestelling als brievenbuspakje wordt verzonden.

![PostNL Brievenbuspakje]({{ site.baseurl }}/assets/images/3_bezorging_brievenbuspakje.png "PostNL Brievenbuspakje")

**Automatisch**

Om een pakketje automatisch als brievenbuspakje te markeren zijn een paar punten belangrijk:

- Bij de producten dient het gewicht correct ingesteld te zijn, het maximale gewicht van een brievenbuspakje is 2 kg.
- Bij de producten moet onder de tab PostNL `Soort` op *Brievenbuspakje* gezet worden en de `Maximale hoeveelheid Brievenbuspakje` moet ingesteld zijn.

![PostNL Brievenbuspakje productinstellingen]({{ site.baseurl }}/assets/images/3_bezorging_brievenbuspakje_productinstellingen.png "PostNL Brievenbuspakje productinstellingen")

**Handmatig**

Bij de handmatige optie kan een bestelling op twee plaatsen aangemerkt worden als brievenbuspakje:
- **Bestellingen overzicht:** In het bestellingen overzicht kunt u van één of meerdere bestellingen de verzendoptie wijzigen. Selecteer de bestellingen waarvan u de verzendoptie wil wijzigen, selecteer in de PostNL actiebalk ‘Verander productcode’ en selecteer vervolgens de bezorgoptie *Brievenbuspakje Extra*.
- **Per bestelling:** Open hiervoor vanuit het bestellingen overzicht een bestelling en klik op de optie om de bestelling te versturen. Onderaan de pagina kan er vervolgens bij de Verzending opties een *PostNL Bezorgoptie* geselecteerd worden. Kies in dit geval voor *Brievenbuspakje Extra*.
  ![PostNL Brievenbuspakje verzenden]({{ site.baseurl }}/assets/images/3_bezorging_brievenbuspakje_verzenden.png "PostNL Brievenbuspakje verzenden")


### Retour instellingen
Er zijn verschillende retouropties beschikbaar, die ingesteld kunnen worden in het kader Retouren:

![Retour instellingen]({{ site.baseurl }}/assets/images/3_Retouren_instellingen.png "Magento 2 Admin - PostNL Retouren")
