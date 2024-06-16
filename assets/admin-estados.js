import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let modalCrear = document.getElementById('crear-modal-estado');
let modalEliminar = document.getElementById('eliminar-modal-estado');
let abrirCrearEstado = document.getElementById('abrir-modal-crear-estado');
let cerrarCrearEstado = document.getElementById('cerrar-modal-crear-estado');
let abrirEliminarEstado = document.getElementById('abrir-modal-eliminar-estado');
let cerrarEliminarEstado = document.getElementById('cerrar-modal-eliminar-estado');

let modalExito = document.getElementById('exito-modal-estado');
let modalError = document.getElementById('error-modal-estado');
let modalConfirmar = document.getElementById('confirmar-modal-estado');

let table;

abrirCrearEstado.addEventListener('click', () => {
    modalCrear.classList.remove('hidden');

    let botonCrearestado = document.getElementById('boton-crear-estado');

    botonCrearestado.addEventListener('click', () => {
        let nombreCrear = document.getElementById('nombreCrear').value;

        let datos = new FormData();
        datos.append('nombre', nombreCrear);

        const options = {
            method: "POST",
            body: datos
        };

        fetch('/estado/crear', options)
        .then(response => response.json())
        .then(data => {
            if (data.response !== 'error') {
                modalCrear.classList.add('hidden');
                let mensajeModalExito = document.getElementById('mensaje-exito-modal-estado');
                mensajeModalExito.innerHTML = 'El estado se creó correctamente.';
                let cerrarModalExito = document.getElementById('cerrar-exito-modal-estado');
                cerrarModalExito.addEventListener('click', () => {
                    modalExito.classList.add('hidden');
                });
                modalExito.classList.remove('hidden');
            } else {
                modalCrear.classList.add('hidden');
                let mensajeModalError = document.getElementById('mensaje-error-modal-estado');
                mensajeModalError.innerHTML = data.error;
                let cerrarModalError = document.getElementById('cerrar-error-modal-estado');
                cerrarModalError.addEventListener('click', () => {
                    modalError.classList.add('hidden');
                });
                modalError.classList.remove('hidden');
            }
        })
        .catch(error => console.log(error));
    }, { once: true });
});

cerrarCrearEstado.addEventListener('click', () => {
    modalCrear.classList.add('hidden');
});

abrirEliminarEstado.addEventListener('click', () => {
    let datatableEliminar = document.getElementById('eliminar-datatable-estados');

    fetch('/estado/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            table = new DataTable(datatableEliminar, {
                bLengthChange: false,
                data: data,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' }
                ]
            });

            modalEliminar.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black');
            datatableEliminar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl');
            datatableEliminar.children[3].remove();

            document.querySelectorAll('#eliminar-datatable-estados tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    modalEliminar.classList.add('hidden');
                    modalConfirmar.classList.remove('hidden');
                    let mensajeModalConfirmar = document.getElementById('mensaje-confirmar-modal-estado');
                    mensajeModalConfirmar.innerHTML = '¿Estás seguro de que deseas eliminar a el estado ' + rowData.nombre + '?';
                    let cerrarModalConfirmar = document.getElementById('cerrar-confirmar-modal-estado');
                    let aceptarModalConfirmar = document.getElementById('aceptar-confirmar-modal-estado');

                    aceptarModalConfirmar.addEventListener('click', () => {
                        fetch('/estado/eliminar/' + rowData.id)
                        .then(response => response.json())
                        .then(data => {
                            if (data.response !== 'error') {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalExito = document.getElementById('mensaje-exito-modal-estado');
                                mensajeModalExito.innerHTML = 'El estado se eliminó correctamente.';
                                let cerrarModalExito = document.getElementById('cerrar-exito-modal-estado');
                                cerrarModalExito.addEventListener('click', () => {
                                    modalExito.classList.add('hidden');
                                });
                                modalExito.classList.remove('hidden');
                            } else {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalError = document.getElementById('mensaje-error-modal-estado');
                                mensajeModalError.innerHTML = data.error;
                                let cerrarModalError = document.getElementById('cerrar-error-modal-estado');
                                cerrarModalError.addEventListener('click', () => {
                                    modalError.classList.add('hidden');
                                });
                                modalError.classList.remove('hidden');
                            }
                            table.destroy();
                        })
                        .catch(error => console.log(error));
                    }, { once: true });

                    cerrarModalConfirmar.addEventListener('click', () => {
                        modalConfirmar.classList.add('hidden');
                        table.destroy();
                    });
                }, { once: true });
            });

            datatableEliminar.classList.remove('hidden');
            modalEliminar.classList.remove('hidden');
        } else {
            modalError.classList.remove('hidden');
            let cerrarModalError = document.getElementById('cerrar-error-modal-estado');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEliminarEstado.addEventListener('click', () => {
    modalEliminar.classList.add('hidden');
    table.destroy();
});