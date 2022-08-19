---
navigation: false
bodyclass: error-code-page
---
# PostNL Magento 2 Error Codes

Voor specifieke gevallen kan een artikel geschreven zijn. Hieronder vind je een lijst met artikelen, gebruik de zoekbalk om een foutcode eenvoudig te vinden.

<input type="text" id="search" placeholder="Search..."/>



{% for page in site.pages %}
{% if page.dir == "/error-codes/" %}
<div class="block" data-code="{{page.code | uri_escape}}" data-title="{{page.title | uri_escape}}">
    <h2 id="{{page.code}}" data-navigation-title="{{page.code}}">[{{page.code}}] {{page.title}}</h2>
    <p>{{page.content}}</p>
</div>
{% endif %}
{% endfor %}

<script src="{{ site.baseurl }}/assets/js/search.js"></script>
