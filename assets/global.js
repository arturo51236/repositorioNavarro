let main = document.getElementsByTagName('main')[0].firstElementChild;
let modalError = document.getElementById('error-modal-producto');

document.addEventListener('click', function(event) {
    const menu = document.getElementById('menu');
    const btnMenu = document.getElementById('btn-menu');
    const btnCategorias1 = document.getElementById('btn-categorias-1');
    const btnCategorias2 = document.getElementById('btn-categorias-2');
    const mostrarCategorias = document.getElementById('mostrar-categorias');

    if (!menu.classList.contains('hidden') && !menu.contains(event.target) && event.target !== btnMenu) {
        menu.classList.toggle('hidden');
        if (main.parentElement.classList.contains('first-room-main')) {
            main.parentElement.classList.toggle('bg-black/60');
        } else {
            main.classList.toggle('bg-black/60');
        }
        toggleTransparency();
        toggleProductInfoTransparency();
    }

    if (mostrarCategorias && !mostrarCategorias.contains(event.target) && event.target !== btnCategorias1 && event.target !== btnCategorias2) {
        if (main.parentElement.classList.contains('first-room-main')) {
            main.parentElement.classList.toggle('bg-black/60');
        } else {
            main.classList.toggle('bg-black/60');
        }
        mostrarCategorias.remove();
        toggleTransparency();
        toggleProductInfoTransparency();
    }
});

document.getElementById('btn-menu').addEventListener('click', function(event) {
    const mostrarCategorias = document.getElementById('mostrar-categorias');

    if (mostrarCategorias) {
        mostrarCategorias.remove();
    } else {
        if (main.parentElement.classList.contains('first-room-main')) {
            main.parentElement.classList.toggle('bg-black/60');
        } else {
            main.classList.toggle('bg-black/60');
        }
        toggleTransparency();
        toggleProductInfoTransparency();
    }

    const menu = document.getElementById('menu');
    menu.classList.toggle('hidden');
    event.stopPropagation();
});

function handleCategoriasClick(event) {
    fetch('/categoria/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            const mostrarCategorias = document.getElementById('mostrar-categorias');

            if (mostrarCategorias) {
                if (main.parentElement.classList.contains('first-room-main')) {
                    main.parentElement.classList.toggle('bg-black/60');
                } else {
                    main.classList.toggle('bg-black/60');
                }
                mostrarCategorias.remove();
            } else {
                if (main.parentElement.classList.contains('first-room-main')) {
                    main.parentElement.classList.toggle('bg-black/60');
                } else {
                    main.classList.toggle('bg-black/60');
                }
                let divCategorias = document.createElement('div');
                divCategorias.classList.add('flex', 'flex-row', 'flex-wrap', 'box-border');

                data.forEach((categoria) => {
                    let botonCategoria = document.createElement('button');
                    botonCategoria.addEventListener('click', () => {
                        window.location.href = '/global/productos/categoria/' + categoria.uuid;
                    });
                    let divCategoria = document.createElement('div');
                    let fotoCategoria = document.createElement('img');
                    let nombreCategoria = document.createElement('h3');
                    fotoCategoria.src = '/uploads/categorias/' + categoria.foto;
                    fotoCategoria.classList.add('h-14', 'w-14', 'm-auto');
                    nombreCategoria.innerHTML = categoria.nombre;
                    divCategoria.classList.add('m-3', 'text-center');
                    divCategoria.append(fotoCategoria);
                    divCategoria.append(nombreCategoria);
                    botonCategoria.append(divCategoria);
                    divCategorias.append(botonCategoria);
                });

                divCategorias.id = 'mostrar-categorias';
                divCategorias.classList.add('rounded-3xl', 'bg-white', 'h-1/6', 'lg:h-1/6', 'absolute', 'top-0', 'inset-x-16', 'md:inset-x-44', 'lg:inset-x-56', 'mt-48', 'md:mt-44', 'lg:mt-28', 'z-30', 'justify-center', 'border-4', 'border-indigo-400');
                main.parentElement.appendChild(divCategorias);
            }
            toggleTransparency();
            toggleProductInfoTransparency();
        } else {
            let mensajeModalError = document.getElementById('mensaje-error-modal-producto');
            mensajeModalError.innerHTML = data.error;
            let cerrarModalError = document.getElementById('cerrar-error-modal-producto');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
            modalError.classList.remove('hidden');
        }
    })
    .catch(error => console.log(error));
}

document.getElementById('btn-categorias-1').addEventListener('click', handleCategoriasClick);
document.getElementById('btn-categorias-2').addEventListener('click', handleCategoriasClick);

function toggleTransparency() {
    let tituloNuestrosProductos = document.getElementById('title-nuestros-productos');
    if (tituloNuestrosProductos) {
        const productCards = document.querySelectorAll('#carta-productos-propios');
        tituloNuestrosProductos.classList.toggle('transparent');

        productCards.forEach(card => {
            card.classList.toggle('transparent');
            card.lastElementChild.children[1].classList.toggle('text-gray-900');
            card.lastElementChild.children[1].classList.toggle('text-gray-200');
            card.lastElementChild.lastElementChild.firstElementChild.classList.toggle('text-gray-900');
            card.lastElementChild.lastElementChild.firstElementChild.classList.toggle('text-gray-200');
        });
    }

    let tituloProductoCategoria = document.getElementById('title-productos-categoria');
    if (tituloProductoCategoria) {
        const productCardsCategoria = document.querySelectorAll('#carta-productos-categoria');
        tituloProductoCategoria.classList.toggle('transparent');

        productCardsCategoria.forEach(card => {
            card.classList.toggle('transparent');
            card.lastElementChild.children[1].classList.toggle('text-gray-900');
            card.lastElementChild.children[1].classList.toggle('text-gray-200');
            card.lastElementChild.lastElementChild.firstElementChild.classList.toggle('text-gray-900');
            card.lastElementChild.lastElementChild.firstElementChild.classList.toggle('text-gray-200');
        });
    }

    let tituloCarrito = document.getElementById('title-carrito-usuario');
    if (tituloCarrito) {
        const lineaCarritoUsuario = document.querySelectorAll('#carta-carrito-usuario');
        tituloCarrito.classList.toggle('transparent');

        lineaCarritoUsuario.forEach(card => {
            card.classList.toggle('transparent');
            card.children[2].classList.toggle('bg-gray-100');
            card.children[2].classList.toggle('bg-gray-800');
            card.children[2].classList.toggle('transparent');
            card.children[1].children[0].classList.toggle('text-gray-900');
            card.children[1].children[0].classList.toggle('text-gray-400');
            card.children[1].children[1].classList.toggle('text-gray-900');
            card.children[1].children[1].classList.toggle('text-gray-400');
            card.children[1].lastElementChild.firstElementChild.classList.toggle('text-gray-900');
            card.children[1].lastElementChild.firstElementChild.classList.toggle('text-gray-400');
        });
    }

    let cartaFoto = document.getElementById('carta-foto');
    if (cartaFoto) {
        cartaFoto.classList.toggle('transparent');

        const fotosIconosCarta = document.querySelectorAll('.fotos-iconos-carta');

        fotosIconosCarta.forEach(icon => {
            icon.classList.toggle('transparent');
        });

        const sobreNosotrosTexto1 = document.querySelectorAll('.sobre-nosotros-texto-1');

        sobreNosotrosTexto1.forEach(text => {
            text.classList.toggle('text-gray-200/40');
        });

        const sobreNosotrosTexto2 = document.querySelectorAll('.sobre-nosotros-texto-2');

        sobreNosotrosTexto2.forEach(text => {
            text.classList.toggle('text-indigo-500');
            text.classList.toggle('text-gray-400/60');
        });
    }

    if (main.parentElement.classList.contains('first-room-main')) {
        main.children[0].classList.toggle('transparent');

        const cajasInfoMainRoom = document.querySelectorAll('.cajas-informacion-main-room');

        cajasInfoMainRoom.forEach(caja => {
            caja.classList.toggle('transparent');
            caja.classList.toggle('border-indigo-400');
            caja.classList.toggle('border-indigo-200/50');
            caja.children[0].children[1].classList.toggle('text-gray-200');
        });

        let tituloTodosProductos = document.getElementById('title-productos');

        if (tituloTodosProductos) {
            tituloTodosProductos.classList.toggle('transparent')
        }

        const productCards = document.querySelectorAll('#carta-producto');

        productCards.forEach(card => {
            card.classList.toggle('transparent');
            card.lastElementChild.children[1].classList.toggle('text-gray-900');
            card.lastElementChild.children[1].classList.toggle('text-gray-200');
            card.lastElementChild.lastElementChild.firstElementChild.classList.toggle('text-gray-900');
            card.lastElementChild.lastElementChild.firstElementChild.classList.toggle('text-gray-200');
        });
    }

    let tituloFabricantes = document.getElementById('title-fabricantes');
    if (tituloFabricantes) {
        tituloFabricantes.classList.toggle('transparent');
        main.parentElement.classList.toggle('bg-black/60');

        const cartasFabricantes = document.querySelectorAll('.carta-fabricante');

        cartasFabricantes.forEach(card => {
            card.classList.toggle('transparent');
        });
    }
}

function toggleProductInfoTransparency() {
    const productoInfo = document.getElementById('producto-info');

    if (productoInfo) {
        document.querySelectorAll('.img-producto').forEach((img, index) => {
            img.classList.toggle('opacity-30');
        })

        document.querySelectorAll('.btn-imagen-producto').forEach((btn, index) => {
            btn.classList.toggle('opacity-30');
        })

        document.querySelectorAll('.texto-producto').forEach((texto, index) => {
            texto.classList.toggle('!text-gray-100');
            texto.classList.toggle('opacity-30');
        })

        document.querySelectorAll('.transpa').forEach((transpa, index) => {
            transpa.classList.toggle('opacity-30');
        })
    }
}

document.querySelectorAll('#carta-producto').forEach((carta, index) => {
    carta.addEventListener('click', () => {
        let uuid = carta.lastElementChild.firstElementChild.textContent;

        window.location.href = '/global/producto/' + uuid;
    })
})