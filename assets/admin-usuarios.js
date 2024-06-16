import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let modalEliminar = document.getElementById('eliminar-modal-usuario');
let abrirCrearUsuario = document.getElementById('abrir-modal-crear-usuario');
let abrirEliminarUsuario = document.getElementById('abrir-modal-eliminar-usuario');
let cerrarEliminarUsuario = document.getElementById('cerrar-modal-eliminar-usuario');

let modalExito = document.getElementById('exito-modal-usuario');
let modalError = document.getElementById('error-modal-usuario');
let modalConfirmar = document.getElementById('confirmar-modal-usuario');

let table;

abrirCrearUsuario.addEventListener('click', () => {
    window.location.href = '/registro';
});

abrirEliminarUsuario.addEventListener('click', () => {
    let datatableEliminar = document.getElementById('eliminar-datatable-usuarios');

    fetch('/usuario/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            const transformedData = data.map(item => {
                return {
                    ...item,
                    verified: item.verified ? 'Sí' : 'No'
                };
            });

            table = new DataTable(datatableEliminar, {
                bLengthChange: false,
                data: transformedData,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'apellidos' },
                    { data: 'dni' },
                    { data: 'email' },
                    { data: 'pais' },
                    { data: 'provincia' },
                    { data: 'cp' },
                    { data: 'direccion' },
                    { data: 'verified' },
                    { data: 'foto' }
                ]
            });

            modalEliminar.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black');
            datatableEliminar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl');
            datatableEliminar.children[3].remove();

            document.querySelectorAll('#eliminar-datatable-usuarios tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    modalEliminar.classList.add('hidden');
                    modalConfirmar.classList.remove('hidden');
                    let mensajeModalConfirmar = document.getElementById('mensaje-confirmar-modal-usuario');
                    mensajeModalConfirmar.innerHTML = '¿Estás seguro de que deseas eliminar a el usuario ' + rowData.email + '?';
                    let cerrarModalConfirmar = document.getElementById('cerrar-confirmar-modal-usuario');
                    let aceptarModalConfirmar = document.getElementById('aceptar-confirmar-modal-usuario');

                    aceptarModalConfirmar.addEventListener('click', () => {
                        fetch('/usuario/eliminar/' + rowData.id)
                        .then(response => response.json())
                        .then(data => {
                            if (data.response !== 'error') {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalExito = document.getElementById('mensaje-exito-modal-usuario');
                                mensajeModalExito.innerHTML = 'El usuario se eliminó correctamente.';
                                let cerrarModalExito = document.getElementById('cerrar-exito-modal-usuario');
                                cerrarModalExito.addEventListener('click', () => {
                                    modalExito.classList.add('hidden');
                                });
                                modalExito.classList.remove('hidden');
                            } else {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalError = document.getElementById('mensaje-error-modal-usuario');
                                mensajeModalError.innerHTML = data.error;
                                let cerrarModalError = document.getElementById('cerrar-error-modal-usuario');
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
            let cerrarModalError = document.getElementById('cerrar-error-modal-usuario');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEliminarUsuario.addEventListener('click', () => {
    modalEliminar.classList.add('hidden');
    table.destroy();
});