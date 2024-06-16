import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let mid = document.getElementById('mid');
let modalEditarDatos = document.getElementById('editar-modal-pedido-datos');
let cerrarEditarPedidoDatos = document.getElementById('cerrar-modal-editar-pedido-datos');

let modalExito = document.getElementById('exito-modal-pedido');
let modalError = document.getElementById('error-modal-pedido');

let datatable = document.getElementById('datatable-pedido');

document.addEventListener("DOMContentLoaded", function(event) {
    fetch('/pedido/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            const transformedData = data.map(item => {
                return {
                    ...item,
                    estado: item.estado.nombre,
                    usuario: item.usuario.email
                };
            });

            let table = new DataTable(datatable, {
                bLengthChange: false,
                data: transformedData,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'estado' },
                    { data: 'usuario' },
                    { data: 'fechaCreacion' }
                ]
            });

            document.querySelectorAll('#datatable-pedido tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    fetch('/pedido/obtener/' + rowData.id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.response !== 'error') {
                            let estado = document.getElementById('estadoEditar');

                            const estadosCargados = obtenerYPintarEstados('estadoEditar');

                            if (estadosCargados) {
                                estado.value = rowData.estado;

                                cerrarEditarPedidoDatos.addEventListener('click', () => {
                                    modalEditarDatos.classList.add('hidden');
                                })

                                let botonEditarPedido = document.getElementById('boton-editar-pedido');

                                botonEditarPedido.addEventListener('click', () => {
                                    let estadoEditar = document.getElementById('estadoEditar').value;

                                    let datos = new FormData();
                                    datos.append('estado', estadoEditar);
                                    datos.append('id', rowData.id);

                                    const options = {
                                        method: "POST",
                                        body: datos
                                    };

                                    fetch('/pedido/editar', options)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.response !== 'error') {
                                            modalEditarDatos.classList.add('hidden');
                                            let mensajeModalExito = document.getElementById('mensaje-exito-modal-pedido');
                                            mensajeModalExito.innerHTML = 'El pedido se editó correctamente.';
                                            let cerrarModalExito = document.getElementById('cerrar-exito-modal-pedido');
                                            cerrarModalExito.addEventListener('click', () => {
                                                modalExito.classList.add('hidden');
                                                window.location.reload();
                                            });
                                            modalExito.classList.remove('hidden');
                                        } else {
                                            modalEditarDatos.classList.add('hidden');
                                            let mensajeModalError = document.getElementById('mensaje-error-modal-pedido');
                                            mensajeModalError.innerHTML = data.error;
                                            let cerrarModalError = document.getElementById('cerrar-error-modal-pedido');
                                            cerrarModalError.addEventListener('click', () => {
                                                modalError.classList.add('hidden');
                                            });
                                            modalError.classList.remove('hidden');
                                        }
                                        table.destroy();
                                    }, { once: true })
                                    .catch(error => console.log(error));
                                }, { once: true });

                                modalEditarDatos.classList.remove('hidden');
                            }
                        } else {
                            let mensajeModalError = document.getElementById('mensaje-error-modal-pedido');
                            mensajeModalError.innerHTML = data.error;
                            let cerrarModalError = document.getElementById('cerrar-error-modal-pedido');
                            cerrarModalError.addEventListener('click', () => {
                                modalError.classList.add('hidden');
                            });
                            modalError.classList.remove('hidden');
                        }
                    })
                    .catch(error => console.log(error));
                }, { once: true })
            })

            mid.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-white');
            datatable.classList.remove('hidden');
            document.getElementById('volver-atras').classList.remove('hidden');
            datatable.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl', 'w-full');
            datatable.children[3].remove();
        } else {
            let modalError = document.getElementById('error-modal-pedido');
            modalError.classList.remove('hidden');
            let cerrarModalError = document.getElementById('cerrar-error-modal-pedido');
            cerrarModalError.addEventListener('click', () => {
                window.history.back();
            });
        }
    })
    .catch(error => console.log(error));
});

async function obtenerYPintarEstados(id) {
    try {
        let response = await fetch('/estado/obtener_todos');
        let data = await response.json();

        if (data.response !== 'error') {
            let estadoInput = document.getElementById(id);

            data.forEach((estado) => {
                let option = document.createElement('option');
                option.innerHTML = estado.nombre;
                option.value = estado.id;
                estadoInput.appendChild(option);
            });

            return true;
        } else {
            let mensajeModalError = document.getElementById('mensaje-error-modal-pedido');
            mensajeModalError.innerHTML = data.error;
            let cerrarModalError = document.getElementById('cerrar-error-modal-pedido');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
            modalError.classList.remove('hidden');

            return false;
        }
    } catch (error) {
        return false;
    }
}