---
title: "The PostNL extension is currently being upgraded"
code: POSTNL-0198
---
### Probleem

U krijgt bij het installeren van een extensie de onderstaande melding:

_The PostNL extension is currently being upgraded._

**Oorzaak/Oplossing:**

De product attribuut update cron is draaiende. Wanneer je de extensie installeert, moeten alle producten gewijzigd worden met nieuwe PostNL attributen. Dit kan lang duren, dus doen we het via een tijdelijke cronjob. Zo lang deze melding er staat is dit proces nog niet afgerond. Je kunt in de tussentijd gewoon je shop en de producten beheren en mensen kunnen gewoon bestellen.
