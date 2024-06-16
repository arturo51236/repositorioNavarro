let modalExito = document.getElementById('exito-modal-contacto');
let modalError = document.getElementById('error-modal-contacto');
let modalConfirmar = document.getElementById('confirmar-modal-contacto');

let cerrarModalConfirmar = document.getElementById('cerrar-confirmar-modal-contacto');
let aceptarModalConfirmar = document.getElementById('aceptar-confirmar-modal-contacto');

document.getElementById('enviarCorreo').addEventListener('click', () => {
    modalConfirmar.classList.remove('hidden');
});

aceptarModalConfirmar.addEventListener('click', () => {
    let destinatario = document.getElementById('destinatario').value;
    let asunto = document.getElementById('asunto').value;
    let mensaje = document.getElementById('mensaje').value;

    let datos = new FormData();
    datos.append('destinatario', destinatario);
    datos.append('asunto', asunto);
    datos.append('mensaje', mensaje);

    const options = {
        method: "POST",
        body: datos
    };

    fetch('/contacto/enviar', options)
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            modalConfirmar.classList.add('hidden');
            let mensajeModalExito = document.getElementById('mensaje-exito-modal-contacto');
            mensajeModalExito.innerHTML = 'El correo se envió correctamente.';
            let cerrarModalExito = document.getElementById('cerrar-exito-modal-contacto');
            cerrarModalExito.addEventListener('click', () => {
                modalExito.classList.add('hidden');
            });
            modalExito.classList.remove('hidden');
        } else {
            modalConfirmar.classList.add('hidden');
            let mensajeModalError = document.getElementById('mensaje-error-modal-contacto');
            mensajeModalError.innerHTML = data.error;
            let cerrarModalError = document.getElementById('cerrar-error-modal-contacto');
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