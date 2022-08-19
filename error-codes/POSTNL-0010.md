---
title: "Er is een fout opgetreden tijdens het verwerken van deze actie"
code: POSTNL-0010
---
### Probleem

In de backend is de volgende melding zichtbaar: "An error occurred while processing this action." of "Een fout is opgetreden tijdens het verwerken van deze actie.".

### Oplossing

Kijk in de log bestanden (var/log/TIG\_PostNL/) voor meer informatie.  
De Magento en PostNL logging dienen aan te staan vóór het optreden van de fout om deze fout terug te kunnen vinden in de logs.

De Magento logging kunt u inschakelen in het menu Systeem > Configuratie > Gevorderd > Ontwerper > Log instellingen.

De PostNL logging kunt u inschakelen in het menu Systeem > Configuratie > PostNL > Geavanceerde instellingen > Technische instellingen.
