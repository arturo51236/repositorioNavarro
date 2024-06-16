let botonAnadirCarrito = document.getElementById('anadir-carrito');

let modalExito = document.getElementById('exito-modal-producto');
let modalError = document.getElementById('error-modal-producto');

if (botonAnadirCarrito) {
    botonAnadirCarrito.addEventListener('click', () => {
        let cantidad = document.getElementById('cantidad-producto').value;
        let url = window.location.href;
        let partesUrl = url.split('/');
        let uuidProducto = partesUrl[partesUrl.length - 1];
    
        let datos = new FormData();
        datos.append('cantidad', cantidad);
        datos.append('producto', uuidProducto);
    
        const options = {
            method: "POST",
            body: datos
        };
    
        fetch('/memoriacesta/crear', options)
        .then(response => response.json())
        .then(data => {
            if (data.response !== 'error') {
                let mensajeModalExito = document.getElementById('mensaje-exito-modal-producto');
                mensajeModalExito.innerHTML = 'El producto se añadió al carrito correctamente.';
                let cerrarModalExito = document.getElementById('cerrar-exito-modal-producto');
                cerrarModalExito.addEventListener('click', () => {
                    modalExito.classList.add('hidden');
                });
                modalExito.classList.remove('hidden');
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
    });
}