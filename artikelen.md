---
title: Artikelen
---
# Artikelen
Voor specifieke gevallen kan een artikel geschreven zijn. Hieronder vind je een lijst met artikelen
<ul>
  {% for post in site.posts %}
    <li>
      <a href="{{ post.url }}">{{ post.title }}</a>
    </li>
  {% endfor %}
</ul>
