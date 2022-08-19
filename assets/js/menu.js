const headers = document.querySelectorAll("h1, h2, h3, h4, h5, h6");

function buildList(list, level = 1){
    const listItems = [];
    for (const index in list) {
        const element = list[index];
        if (element.tagName !== "H" + level || !element.id){
            continue;
        }
        const subItems = [];
        for (var remainderIndex = parseInt(index) + 1; remainderIndex < list.length; remainderIndex++){
            console.log(list[remainderIndex]);
            if (list[remainderIndex].tagName == "H" + level) {
                break;
            }
            subItems.push(list[remainderIndex]);
        }
        var text = element.innerText;
        if ('navigationTitle' in element.dataset) {
            text = element.dataset.navigationTitle;
        }
        listItems.push("<li><a href='#" + element.id + "'>" + text + "</a>" + buildList(subItems, level + 1) + "</li>");
    }
    if (listItems.length == 0){
        return '';
    }
    return '<ul>' + listItems.join("") + "</ul>";
}
function handleScroll() {
    const sectionMargin = 100;
    const current = headers.length - [...headers].reverse().findIndex((section) => window.scrollY >= section.offsetTop - sectionMargin ) - 1;
    const links = document.querySelectorAll("nav a");
    links.forEach((el) => el.getAttribute('href') === "#" + headers[current].id ? el.classList.add('active') : el.classList.remove('active'));
}

const sidebar = document.getElementById('sidebar');
if (sidebar) {
    const navigationHtml = buildList(headers);
    sidebar.innerHTML = navigationHtml;
    sidebar.classList.add('active');

    window.addEventListener("scroll", handleScroll);
}
