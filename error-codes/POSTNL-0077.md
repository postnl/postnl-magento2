---
title: "Niet mogelijk om Track&Trace e-mail te sturen"
code: POSTNL-0077
---

### Probleem

Het is momenteel niet mogelijk om voor deze zending de track & trace e-mail te versturen.

### Oplossing

Dit kan o.a. voorkomen als de Track & Trace e-mail al verzonden is voor deze zending of als het geselecteerde e-mail template niet beschikbaar is.

Vanaf versie 1.4.1 is het een bekend probleem dat de Track&Trace e-mail niet verzonden kan worden. Dit komt meestal doordat, tijdens de upgrade, de standaard template niet kan worden gevonden door Magento. Om dit op te lossen, kan het onderstaande stappenplan gevolgd worden:Â 

\- Navigeer naar Systeem->Transactionele E-Mails.Â 

\- Selecteer hier als sjabloon 'PostNL Track & Trace e-mail'Â 

\- Laad het sjabloon in

\- Geef het template een naam

\- Sla het sjabloon op![]({{site.baseurl}}/assets/images/POSTNL-0077_0.png)

Â 

\- Navigeer naar Systeem->configuratie->PostNL->Geavanceerde instellingen->Track&Trace

\- Selecteer het nieuw gemaakte Track &Â Trace template als gebruikte template

\- Sla de configuratie op

![]({{site.baseurl}}/assets/images/POSTNL-0077_1.png)
