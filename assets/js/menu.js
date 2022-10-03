const headers = document.querySelectorAll("h1, h2, h3, h4, h5, h6");

function buildList(list, level = 1, chapter = [0]){
    const startNumberAtlevel = 2;
    const listItems = [];
    for (const index in list) {
        const element = list[index];
        if (element.tagName !== "H" + level || !element.id){
            continue;
        }
        const subItems = [];
        for (var remainderIndex = parseInt(index) + 1; remainderIndex < list.length; remainderIndex++){
            if (list[remainderIndex].tagName === "H" + level) {
                break;
            }
            subItems.push(list[remainderIndex]);
        }
        // Increment chapter
        if (!( level - startNumberAtlevel in chapter)) {
            chapter[level-startNumberAtlevel] = 0;
        }

        if (level >= startNumberAtlevel) {
            chapter[level-startNumberAtlevel] += 1;
            const chapterNumber = chapter.slice(0,level).join('.');
            element.innerText = chapterNumber + " " + element.innerText;
        }

        var text = element.innerText;
        if ('navigationTitle' in element.dataset) {
            text = element.dataset.navigationTitle;
        }

        const chapterSub = chapter.slice(0,level);
        listItems.push("<li><a href='#" + element.id + "'>" + text + "</a>" + buildList(subItems, level + 1, chapterSub) + "</li>");
    }

    if (listItems.length === 0){
        return '';
    }

    return '<ul>' + listItems.join("") + "</ul>";
}


function getParents(elem, selector) {
    // Element.matches() polyfill
    if (!Element.prototype.matches) {
        Element.prototype.matches =
            Element.prototype.matchesSelector ||
            Element.prototype.mozMatchesSelector ||
            Element.prototype.msMatchesSelector ||
            Element.prototype.oMatchesSelector ||
            Element.prototype.webkitMatchesSelector ||
            function(s) {
                var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                    i = matches.length;
                while (--i >= 0 && matches.item(i) !== this) {}
                return i > -1;
            };
    }

    // Set up a parent array
    var parents = [];

    // Push each parent element to the array
    for ( ; elem && elem !== document; elem = elem.parentNode ) {
        if (selector) {
            if (elem.matches(selector)) {
                parents.push(elem);
            }
            continue;
        }
        parents.push(elem);
    }

    // Return our parent array
    return parents;
}

function handleScroll() {
    const sectionMargin = 120;
    const current = headers.length - [...headers].reverse().findIndex((section) => window.scrollY >= section.offsetTop - sectionMargin ) - 1;
    const links = document.querySelectorAll("nav a");

    var activeLink = null;
    links.forEach(function(el) {
        el.classList.remove('active')
        if (el.getAttribute('href') === "#" + headers[current].id) {
            el.classList.add('active');
            activeLink = el;
        }
    });

    if (activeLink) {
        const parents = getParents(activeLink, 'ul, li');
        parents.forEach((el) => el.classList.add('expand'));
        const all = document.querySelectorAll("nav ul.expand, nav li.expand");
        all.forEach(function(el) {
            if (!parents.includes(el)) {
                el.classList.remove('expand');
            }
        });
    }

}


const sidebar = document.getElementById('sidebar');
if (sidebar) {
    const navigationHtml = buildList(headers);
    sidebar.innerHTML = navigationHtml;
    sidebar.classList.add('active');

    window.addEventListener("scroll", handleScroll);
}
