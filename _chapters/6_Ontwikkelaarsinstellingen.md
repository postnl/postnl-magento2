## Ontwikkelaarsinstellingen

### Logging
Er zijn een aantal verschillende eigenschappen die meegegeven kunnen worden in de logging. Dit kan handig zijn als er een probleem met de extensie onderzocht moet worden.

De logging instellingen zijn terug te vinden via *Stores → Configuration → Sales → PostNL → Logging*.

**DEBUG:** Uitgebreide debug informatie.

**INFO:** Interessante gebeurtenissen die plaatsvinden.

**NOTICE:** Normale maar belangrijke processen.

**WARNING:** Uitzonderlijke voorvallen welke geen errors zijn.

**ERROR:** Foutmeldingen die niet direct verholpen dienen te worden maar wel gemonitord/gelogged blijven worden.

**CRITICAL:** Kritische fouten.
>Bijvoorbeeld: Onderdeel van een extensie is onbeschikbaar.
 
**ALERT:** Er dient direct actie ondernomen te worden.
>Bijvoorbeeld: Website is down of database is onbereikbaar.

**EMERGENCY:** Systeem is onbruikbaar.

![Logging]({{ site.baseurl }}/assets/images/6_logging.png "PostNL Magento 2 logging")