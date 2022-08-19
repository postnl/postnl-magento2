---
title: "Cron werkt niet correct"
code: POSTNL-0006
---

### Probleem

Boven elke pagina in de backend staat:

"PostNL: It appears that your cron is not working properly. PostNL requires the cron to be active in order to function."

"PostNL: Uw cron lijkt niet correct te functioneren. PostNL heeft de cron nodig om correct te functioneren."

### Oplossing

Er zijn twee methoden om uw klanten te voorzien van Track & Trace informatie:

1\. Automatische e-mails op basis van de cronjob.

2\. Handmatige e-mail op basis van de button "Send tracking information".

In de configuratie van de extensie kunt u kiezen welke methode u wilt gebruiken in het tabblad "Track& Trace" van de Geavanceerde instellingen.  
Ook zonder werkende cron kunt u dus uw klanten voorzien van Track&Trace informatie. Wij adviseren andere niet zichtbare functionaliteiten van de extensie wel om de Magento cron te laten draaien.

De PostNL extensie maakt gebruik van enkele cronjobs. Deze zijn nodig om de statussen van zendingen bij periodiek bij te werken indien u hiervoor heeft gekozen in de configuratie van de extensie. De extensie haakt in op de cron functionaliteit van Magento.

Mocht deze melding niet verdwijnen binnen een uur na installatie dan zijn er twee mogelijke oorzaken:  
1\. Er is op de server geen actieve cronjob ingesteld.  
2\. Magento cron functionaliteit is niet actief.

Server cronjob  
De wijze waarop u cronjobs op uw server instelt verschilt per hostingprovider omdat er meerdere webserverbeheer systemen bestaan. Voorbeelden hiervan zijn DirectAdmin, Plesk en Cpanel.  
Hieronder vindt u een voorbeeld van de wijze waarop u een cronjob instelt voor de DirectAdmin van Magento.

1\. Ga naar het account waar de Magento omgeving op draait en klik "Cronjobs" aan.  
  
![]({{site.baseurl}}/assets/images/POSTNL-0006_0.png)

2\. Voor een Magento Community webshop stelt u in dat de cronjob elke 5 minuten geactiveerd moet worden (zoals in onderstaand voorbeeld). Voor een Magento Enterprise stelt u in dat de cronjob elke minuut geactiveerd moet worden.

![]({{site.baseurl}}/assets/images/POSTNL-0006_1.png)

3\. Controleer met SSH of u de cronjob kunt activeren.  
Dit kan door in een terminal venster in te loggen en het command in te voeren. In ons voorbeeld is dat "/home/magento/public\_html/cron.sh". Dit dient u te doen met root rechten.

Mocht dit niet werken of mocht uw server geen shell mogelijkheid hebben dan kunt u uitwijken naar het cron.php bestand.Â 

In dat geval dient u twee cronjobs aan te maken. U dient aan te geven waar PHP draait voor de opdrachten. U kunt deze locatie achterhalen met de PHP constante PHP\_BINDIR.

Achter de opdrachten dient u een parameter te plaatsen:

Opdracht 1: cron.php -malways 1

Opdracht 2: cron.php -mdefault 1

Zie onderstaand screenshot voor een voorbeeld.

![]({{site.baseurl}}/assets/images/POSTNL-0006_2.png)

4\. Zorg dat uw cron.sh (of cron.php) bestand 755 rechten heeft.

De server roept nu elke 5 minuten (Community Edition) of elke minuut (Enterprise Edition) de cron functionaliteit van Magento aan.

Magento cron

1\. Ga naar het menu System > Configuration > Advanced > System > Cron (Scheduled Tasks).

2\. Zorg dat de instellingen gelijk zijn aan onderstaande afbeelding.

![]({{site.baseurl}}/assets/images/POSTNL-0006_3.png)

Nadat de cron actief is kan het enige tijd duren voor de melding verdwijnt, er zal namelijk een wachtrij aan taken voor de cron zijn.Â   
U kunt het best de database openen en de table "cron\_schedule" openen. Die zal direct gevuld worden en daarin kunt u zien of de aanpassingen effect hebben.
