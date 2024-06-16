let modalExito = document.getElementById('exito-modal-carrito');
let modalError = document.getElementById('error-modal-carrito');
let modalConfirmar = document.getElementById('confirmar-modal-compra');
let cerrarModalConfirmar = document.getElementById('cerrar-confirmar-modal-compra');
let aceptarModalConfirmar = document.getElementById('aceptar-confirmar-modal-compra');
let confirmarCompra = document.getElementById('confirmar-compra');

document.querySelectorAll('#eliminar-del-carrito').forEach((lineaCarrito) => {
    lineaCarrito.addEventListener('click', () => {
        let uuidLineaCarrito = lineaCarrito.children[0].value;

        fetch('/memoriacesta/eliminar/' + uuidLineaCarrito)
            .then(response => response.json())
            .then(data => {
                if (data.response !== 'error') {
                    lineaCarrito.parentElement.remove();

                    let nuevoPrecioTotal = 0;
                    document.querySelectorAll('#carta-carrito-usuario').forEach((cartLine) => {
                        let cantidad = parseInt(cartLine.children[1].children[1].innerText.split(': ')[1]);
                        let precio = parseInt(cartLine.children[1].children[2].children[0].innerText.split(' ')[0]);
                        nuevoPrecioTotal += cantidad * precio;
                    });

                    let precioTotal = document.getElementById('resumen-carrito').children[0].children[0];
                    precioTotal.innerHTML = 'Precio total: ' + nuevoPrecioTotal + ' €';

                    let mensajeModalExito = document.getElementById('mensaje-exito-modal-carrito');
                    mensajeModalExito.innerHTML = 'El producto se eliminó correctamente de la cesta.';
                    let cerrarModalExito = document.getElementById('cerrar-exito-modal-carrito');
                    cerrarModalExito.addEventListener('click', () => {
                        modalExito.classList.add('hidden');
                    });
                    modalExito.classList.remove('hidden');
                } else {
                    let mensajeModalError = document.getElementById('mensaje-error-modal-carrito');
                    mensajeModalError.innerHTML = data.error;
                    let cerrarModalError = document.getElementById('cerrar-error-modal-carrito');
                    cerrarModalError.addEventListener('click', () => {
                        modalError.classList.add('hidden');
                    });
                    modalError.classList.remove('hidden');
                }
            })
            .catch(error => console.log(error));
    })
});

if (confirmarCompra) {
    confirmarCompra.addEventListener('click', () => {
        modalConfirmar.classList.remove('hidden');
    });
}

aceptarModalConfirmar.addEventListener('click', () => {
    let url = window.location.href;
    let partesUrl = url.split('/');
    let uuidUsuario = partesUrl[partesUrl.length - 1];

    fetch('/pedido/crear/' + uuidUsuario)
        .then(response => response.json())
        .then(data => {
            if (data.response !== 'error') {
                let idPedido = data.id;
                let cartas = document.querySelectorAll('#carta-carrito-usuario');

                let promesas = Array.from(cartas).map(async lineaCarrito => {
                    let cantidad = parseInt(lineaCarrito.children[1].children[1].innerText.split(': ')[1]);
                    let uuidProducto = lineaCarrito.children[1].children[2].children[2].value;

                    let datos = new FormData();
                    datos.append('cantidad', cantidad);
                    datos.append('producto', uuidProducto);
                    datos.append('pedido', idPedido);

                    const options = {
                        method: "POST",
                        body: datos
                    };

                    const response = await fetch('/lineapedido/crear', options);
                    return await response.json();
                });

                Promise.all(promesas)
                    .then(resultados => {
                        let errores = resultados.filter(result => result.response === 'error');
                        if (errores.length === 0) {
                            let promesasEliminacion = Array.from(document.querySelectorAll('#eliminar-del-carrito')).map(lineaCarrito => {
                                let uuidLineaCarrito = lineaCarrito.children[0].value;
                                return fetch('/memoriacesta/eliminar/' + uuidLineaCarrito)
                                    .then(response => response.json());
                            });

                            Promise.all(promesasEliminacion)
                                .then(resultadosEliminacion => {
                                    let erroresEliminacion = resultadosEliminacion.filter(result => result.response === 'error');
                                    if (erroresEliminacion.length === 0) {
                                        modalConfirmar.classList.add('hidden');
                                        let mensajeModalExito = document.getElementById('mensaje-exito-modal-carrito');
                                        mensajeModalExito.innerHTML = 'El pedido se realizó correctamente.';
                                        let cerrarModalExito = document.getElementById('cerrar-exito-modal-carrito');
                                        cerrarModalExito.addEventListener('click', () => {
                                            modalExito.classList.add('hidden');
                                            window.location.reload();
                                        });
                                        modalExito.classList.remove('hidden');
                                    } else {
                                        let mensajeModalError = document.getElementById('mensaje-error-modal-carrito');
                                        mensajeModalError.innerHTML = 'Ocurrió un error al eliminar algunos artículos del carrito.';
                                        let cerrarModalError = document.getElementById('cerrar-error-modal-carrito');
                                        cerrarModalError.addEventListener('click', () => {
                                            modalError.classList.add('hidden');
                                        });
                                        modalError.classList.remove('hidden');
                                    }
                                })
                                .catch(error => console.log(error));
                        } else {
                            let mensajeModalError = document.getElementById('mensaje-error-modal-carrito');
                            mensajeModalError.innerHTML = 'Ocurrió un error al crear el pedido.';
                            let cerrarModalError = document.getElementById('cerrar-error-modal-carrito');
                            cerrarModalError.addEventListener('click', () => {
                                modalError.classList.add('hidden');
                            });
                            modalError.classList.remove('hidden');
                        }
                    })
                    .catch(error => console.log(error));
            } else {
                let mensajeModalError = document.getElementById('mensaje-error-modal-carrito');
                mensajeModalError.innerHTML = data.error;
                let cerrarModalError = document.getElementById('cerrar-error-modal-carrito');
                cerrarModalError.addEventListener('click', () => {
                    modalError.classList.add('hidden');
                });
                modalError.classList.remove('hidden');
            }
        })
        .catch(error => console.log(error));
});

cerrarModalConfirmar.addEventListener('click', () => {
    modalConfirmar.classList.add('hidden');
});