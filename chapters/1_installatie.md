## Installatie van de extensie

Voordat er gestart wordt met de installatie van de officiële PostNL Magento 2 extensie of het updaten daarvan adviseren wij altijd om eerst een backup te maken en de extensie eerst op een staging of test omgeving te installeren.

### Installatie met composer (aanbevolen)
Wij adviseren net zoals Magento om de extensie te installeren middels composer. Log hiervoor in middels SSH op uw server en navigeer naar de Magento 2 installatie. Voer hier vervolgens de onder staande commando's uit.

 1. Installeer de extensie
    ```shell
    composer require tig/postnl-magento2
    ```
 2. PostNL Magento 2 extensie inschakelen
    ```shell
    php bin/magento module:enable TIG_PostNL
    ```
 3. Update de Magento 2 omgeving
    ```shell
    php bin/magento setup:upgrade
    ```
 4. *Alleen productie modus* Wanneer de Magento 2 webshop in production mode draait, dan moet ook de static content opnieuw worden gedeployed:
    ```shell
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy
    ```
 
### Handmatige installatie

Het is ook mogelijk om de extensie handmatig te installeren, doorloop daarvoor de volgende stappen:

 1. Download de extensie direct vanuit de github repository door op Code en dan Download ZIP te klikken. Of bovenaan deze pagina *Download .zip* te klikken.
 2. Maak een folder aan in de root folder waar de Magento installatie zich bevindt: `app/code/TIG/PostNL` (Hoofdlettergevoelig)
 3. De inhoud van de zip dient vervolgens in de folder `app/code/TIG/PostNL` geplaatst te worden 
 4. PostNL Magento 2 extensie inschakelen
   ```shell
   php bin/magento module:enable TIG_PostNL
   ```
 5. PostNL Magento 2 extensie inschakelen
    ```shell
    php bin/magento setup:upgrade
    ```
 6. *Alleen productie modus* Wanneer de Magento 2 webshop in production mode draait, dan moet ook de static content opnieuw worden gedeployed:
    ```shell
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy
    ```

### Extensie updaten

Om de PostNL Magento 2 extensie te updaten dienen de volgende commando's uitgevoerd te worden:

```shell
composer update tig/postnl-magento2
php bin/magento setup:upgrade
```

### Extensie deïnstalleren
Om de PostNL Magento 2 extensie te deïnstalleren kan er gebruik gemaakt worden van de deïnstallatie commando's die [Magento aanbiedt](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-uninstall-mods.html#instgde-cli-uninst-mod-uninst)

De extensie maakt gebruik van deïnstallatiescripts. Voeg de `--remove-data` to aan het commando, het script zal dan vragen of order gerelateerde PostNL gegevens verwijderd dienen te worden. Het verwijderen van deze gegevens is optioneel. Het aanbevolen deïstallatiescript is alsvolgt:
```shell
php bin/magento module:uninstall TIG_PostNL --backup-db --remove-data --clear-static-content
```
