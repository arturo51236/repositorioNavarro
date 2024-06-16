import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let modalCrear = document.getElementById('crear-modal-fabricante');
let modalEliminar = document.getElementById('eliminar-modal-fabricante');
let modalEditarTabla = document.getElementById('editar-modal-fabricante-tabla');
let modalEditarDatos = document.getElementById('editar-modal-fabricante-datos');
let abrirCrearFabricante = document.getElementById('abrir-modal-crear-fabricante');
let cerrarCrearFabricante = document.getElementById('cerrar-modal-crear-fabricante');
let abrirEliminarFabricante = document.getElementById('abrir-modal-eliminar-fabricante');
let cerrarEliminarFabricante = document.getElementById('cerrar-modal-eliminar-fabricante');
let abrirEditarFabricanteTabla = document.getElementById('abrir-modal-editar-fabricante-tabla');
let cerrarEditarFabricanteTabla = document.getElementById('cerrar-modal-editar-fabricante-tabla');
let cerrarEditarFabricanteDatos = document.getElementById('cerrar-modal-editar-fabricante-datos');

let modalExito = document.getElementById('exito-modal-fabricante');
let modalError = document.getElementById('error-modal-fabricante');
let modalConfirmar = document.getElementById('confirmar-modal-fabricante');

let table;

abrirCrearFabricante.addEventListener('click', () => {
    modalCrear.classList.remove('hidden');

    let botonCrearFabricante = document.getElementById('boton-crear-fabricante');

    botonCrearFabricante.addEventListener('click', () => {
        let fotoCrear = document.getElementById('fotoCrear').files[0];
        let nombreCrear = document.getElementById('nombreCrear').value;
        let descripcionCrear = document.getElementById('descripcionCrear').value;

        let datos = new FormData();
        datos.append('nombre', nombreCrear);
        datos.append('foto', fotoCrear);
        datos.append('descripcion', descripcionCrear);

        const options = {
            method: "POST",
            body: datos
        };

        fetch('/fabricante/crear', options)
        .then(response => response.json())
        .then(data => {
            if (data.response !== 'error') {
                modalCrear.classList.add('hidden');
                let mensajeModalExito = document.getElementById('mensaje-exito-modal-fabricante');
                mensajeModalExito.innerHTML = 'El fabricante se creó correctamente.';
                let cerrarModalExito = document.getElementById('cerrar-exito-modal-fabricante');
                cerrarModalExito.addEventListener('click', () => {
                    modalExito.classList.add('hidden');
                });
                modalExito.classList.remove('hidden');
            } else {
                modalCrear.classList.add('hidden');
                let mensajeModalError = document.getElementById('mensaje-error-modal-fabricante');
                mensajeModalError.innerHTML = data.error;
                let cerrarModalError = document.getElementById('cerrar-error-modal-fabricante');
                cerrarModalError.addEventListener('click', () => {
                    modalError.classList.add('hidden');
                });
                modalError.classList.remove('hidden');
            }
        })
        .catch(error => console.log(error));
    }, { once: true });
});

cerrarCrearFabricante.addEventListener('click', () => {
    modalCrear.classList.add('hidden');
});

abrirEliminarFabricante.addEventListener('click', () => {
    let datatableEliminar = document.getElementById('eliminar-datatable-fabricantes');

    fetch('/fabricante/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            table = new DataTable(datatableEliminar, {
                bLengthChange: false,
                data: data,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'foto' },
                    { data: 'descripcion' }
                ]
            });

            modalEliminar.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black');
            datatableEliminar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl');
            datatableEliminar.children[3].remove();

            document.querySelectorAll('#eliminar-datatable-fabricantes tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    modalEliminar.classList.add('hidden');
                    modalConfirmar.classList.remove('hidden');
                    let mensajeModalConfirmar = document.getElementById('mensaje-confirmar-modal-fabricante');
                    mensajeModalConfirmar.innerHTML = '¿Estás seguro de que deseas eliminar a el fabricante ' + rowData.nombre + '?';
                    let cerrarModalConfirmar = document.getElementById('cerrar-confirmar-modal-fabricante');
                    let aceptarModalConfirmar = document.getElementById('aceptar-confirmar-modal-fabricante');

                    aceptarModalConfirmar.addEventListener('click', () => {
                        fetch('/fabricante/eliminar/' + rowData.id)
                        .then(response => response.json())
                        .then(data => {
                            if (data.response !== 'error') {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalExito = document.getElementById('mensaje-exito-modal-fabricante');
                                mensajeModalExito.innerHTML = 'El fabricante se eliminó correctamente.';
                                let cerrarModalExito = document.getElementById('cerrar-exito-modal-fabricante');
                                cerrarModalExito.addEventListener('click', () => {
                                    modalExito.classList.add('hidden');
                                });
                                modalExito.classList.remove('hidden');
                            } else {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalError = document.getElementById('mensaje-error-modal-fabricante');
                                mensajeModalError.innerHTML = data.error;
                                let cerrarModalError = document.getElementById('cerrar-error-modal-fabricante');
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
            let cerrarModalError = document.getElementById('cerrar-error-modal-fabricante');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEliminarFabricante.addEventListener('click', () => {
    modalEliminar.classList.add('hidden');
    table.destroy();
});

abrirEditarFabricanteTabla.addEventListener('click', () => {
    let datatableEditar = document.getElementById('editar-datatable-fabricantes');

    fetch('/fabricante/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            table = new DataTable(datatableEditar, {
                bLengthChange: false,
                data: data,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'foto' },
                    { data: 'descripcion' }
                ]
            });

            modalEditarTabla.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black');
            datatableEditar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl');
            datatableEditar.children[3].remove();

            document.querySelectorAll('#editar-datatable-fabricantes tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    fetch('/fabricante/obtener/' + rowData.id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.response !== 'error') {
                            modalEditarTabla.classList.add('hidden');
                            let fotoAnterior = document.getElementById('fotoAnterior');
                            let nombreEditar = document.getElementById('nombreEditar');
                            let descripcionEditar = document.getElementById('descripcionEditar');

                            if (rowData.foto != null) {
                                fotoAnterior.src = '/uploads/fabricantes/' + rowData.foto;
                            } else {
                                fotoAnterior.src = '/static/images/null.png';
                            }
                            nombreEditar.value = rowData.nombre;
                            descripcionEditar.value = rowData.descripcion;

                            cerrarEditarFabricanteDatos.addEventListener('click', () => {
                                modalEditarDatos.classList.add('hidden');
                                modalEditarTabla.classList.remove('hidden');
                            })

                            let botonEditarFabricante = document.getElementById('boton-editar-fabricante');

                            botonEditarFabricante.addEventListener('click', () => {
                                let fotoEditar = document.getElementById('fotoEditar').files[0];
                                let nombreEditar = document.getElementById('nombreEditar').value;
                                let descripcionEditar = document.getElementById('descripcionEditar').value;

                                let datos = new FormData();
                                datos.append('nombre', nombreEditar);
                                datos.append('foto', fotoEditar);
                                datos.append('idAntiguo', rowData.id);
                                datos.append('descripcion', descripcionEditar);

                                const options = {
                                    method: "POST",
                                    body: datos
                                };

                                fetch('/fabricante/editar', options)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.response !== 'error') {
                                        modalEditarDatos.classList.add('hidden');
                                        modalConfirmar.classList.add('hidden');
                                        let mensajeModalExito = document.getElementById('mensaje-exito-modal-fabricante');
                                        mensajeModalExito.innerHTML = 'El fabricante se editó correctamente.';
                                        let cerrarModalExito = document.getElementById('cerrar-exito-modal-fabricante');
                                        cerrarModalExito.addEventListener('click', () => {
                                            modalExito.classList.add('hidden');
                                        });
                                        modalExito.classList.remove('hidden');
                                    } else {
                                        modalEditarDatos.classList.add('hidden');
                                        let mensajeModalError = document.getElementById('mensaje-error-modal-fabricante');
                                        mensajeModalError.innerHTML = data.error;
                                        let cerrarModalError = document.getElementById('cerrar-error-modal-fabricante');
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
                        } else {
                            modalEditarTabla.classList.add('hidden');
                            let mensajeModalError = document.getElementById('mensaje-error-modal-fabricante');
                            mensajeModalError.innerHTML = data.error;
                            let cerrarModalError = document.getElementById('cerrar-error-modal-fabricante');
                            cerrarModalError.addEventListener('click', () => {
                                modalError.classList.add('hidden');
                            });
                            modalError.classList.remove('hidden');
                        }
                    })
                    .catch(error => console.log(error));
                }, { once: true })
            })

            datatableEditar.classList.remove('hidden');
            modalEditarTabla.classList.remove('hidden');
        } else {
            modalError.classList.remove('hidden');
            let cerrarModalError = document.getElementById('cerrar-error-modal-fabricante');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEditarFabricanteTabla.addEventListener('click', () => {
    modalEditarTabla.classList.add('hidden');
    table.destroy();
});