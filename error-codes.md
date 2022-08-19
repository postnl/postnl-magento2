---
navigation: true
bodyclass: error-code-page
---
# PostNL Magento 2 Error Codes

Voor specifieke gevallen kan een artikel geschreven zijn. Hieronder vind je een lijst met artikelen

{% for page in site.pages %}
{% if page.dir == "/error-codes/" %}
<div class="block">
    <h2 id="{{page.code}}" data-navigation-title="{{page.code}}">[{{page.code}}] {{page.title}}</h2>
    <p>{{page.content}}</p>
</div>
{% endif %}
{% endfor %}
