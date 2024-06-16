document.querySelectorAll('#carta-productos-categoria').forEach((carta, index) => {
    carta.addEventListener('click', () => {
        let uuid = carta.lastElementChild.firstElementChild.textContent;

        window.location.href = '/global/producto/' + uuid;
    })
})