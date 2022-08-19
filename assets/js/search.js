const allErrors = document.querySelectorAll('.error-code-page .block');
const searchInput = document.getElementById('search');

if (searchInput) {
    function matches(element, input) {
        return decodeURI(element.dataset.code).includes(input) ||
            decodeURI(element.dataset.title).includes(input);
    }
    searchInput.addEventListener('keyup', function() {
        allErrors.forEach((el) =>
            matches(el, searchInput.value) ?
                el.classList.remove('hide') :
                el.classList.add('hide')
        );
    });
}
