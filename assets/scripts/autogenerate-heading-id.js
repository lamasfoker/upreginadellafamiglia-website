document.addEventListener("DOMContentLoaded", function () {
    const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
    headings.forEach((heading, index) => {
        if (!heading.id) {
            heading.id = 'sezione-' + index;
        }
    });
});
