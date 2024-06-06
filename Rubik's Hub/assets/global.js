document.addEventListener('click', function(event) {
    const menu = document.getElementById('menu');
    const btnMenu = document.getElementById('btn-menu');

    if (!menu.classList.contains('hidden') && !menu.contains(event.target) && event.target !== btnMenu) {
        menu.classList.toggle('hidden');
    }
});

document.getElementById('btn-menu').addEventListener('click', function(event) {
    const menu = document.getElementById('menu');
    menu.classList.toggle('hidden');
    event.stopPropagation();
});