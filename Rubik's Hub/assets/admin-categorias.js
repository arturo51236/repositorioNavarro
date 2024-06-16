import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let modalCrear = document.getElementById('crear-modal-categoria');
let modalEliminar = document.getElementById('eliminar-modal-categoria');
let modalEditarTabla = document.getElementById('editar-modal-categoria-tabla');
let modalEditarDatos = document.getElementById('editar-modal-categoria-datos');
let abrirCrearCategoria = document.getElementById('abrir-modal-crear-categoria');
let cerrarCrearCategoria = document.getElementById('cerrar-modal-crear-categoria');
let abrirEliminarCategoria = document.getElementById('abrir-modal-eliminar-categoria');
let cerrarEliminarCategoria = document.getElementById('cerrar-modal-eliminar-categoria');
let abrirEditarCategoriaTabla = document.getElementById('abrir-modal-editar-categoria-tabla');
let cerrarEditarCategoriaTabla = document.getElementById('cerrar-modal-editar-categoria-tabla');
let cerrarEditarCategoriaDatos = document.getElementById('cerrar-modal-editar-categoria-datos');

let modalExito = document.getElementById('exito-modal-categoria');
let modalError = document.getElementById('error-modal-categoria');
let modalConfirmar = document.getElementById('confirmar-modal-categoria');

let table;

abrirCrearCategoria.addEventListener('click', () => {
    modalCrear.classList.remove('hidden');

    let botonCrearCategoria = document.getElementById('boton-crear-categoria');

    botonCrearCategoria.addEventListener('click', () => {
        let fotoCrear = document.getElementById('fotoCrear').files[0];
        let nombreCrear = document.getElementById('nombreCrear').value;

        let datos = new FormData();
        datos.append('nombre', nombreCrear);
        datos.append('foto', fotoCrear);

        const options = {
            method: "POST",
            body: datos
        };

        fetch('/categoria/crear', options)
        .then(response => response.json())
        .then(data => {
            if (data.response !== 'error') {
                modalCrear.classList.add('hidden');
                let mensajeModalExito = document.getElementById('mensaje-exito-modal-categoria');
                mensajeModalExito.innerHTML = 'La categoría se creó correctamente.';
                let cerrarModalExito = document.getElementById('cerrar-exito-modal-categoria');
                cerrarModalExito.addEventListener('click', () => {
                    modalExito.classList.add('hidden');
                });
                modalExito.classList.remove('hidden');
            } else {
                modalCrear.classList.add('hidden');
                let mensajeModalError = document.getElementById('mensaje-error-modal-categoria');
                mensajeModalError.innerHTML = data.error;
                let cerrarModalError = document.getElementById('cerrar-error-modal-categoria');
                cerrarModalError.addEventListener('click', () => {
                    modalError.classList.add('hidden');
                });
                modalError.classList.remove('hidden');
            }
        })
        .catch(error => console.log(error));
    }, { once: true });
});

cerrarCrearCategoria.addEventListener('click', () => {
    modalCrear.classList.add('hidden');
});

abrirEliminarCategoria.addEventListener('click', () => {
    let datatableEliminar = document.getElementById('eliminar-datatable-categorias');

    fetch('/categoria/obtener_todos')
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
                    { data: 'foto' }
                ]
            });

            modalEliminar.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black', 'w-3/5');
            datatableEliminar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl', 'w-full');
            datatableEliminar.children[3].remove();

            document.querySelectorAll('#eliminar-datatable-categorias tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    modalEliminar.classList.add('hidden');
                    modalConfirmar.classList.remove('hidden');
                    let mensajeModalConfirmar = document.getElementById('mensaje-confirmar-modal-categoria');
                    mensajeModalConfirmar.innerHTML = '¿Estás seguro de que deseas eliminar la categoría ' + rowData.nombre + '?';
                    let cerrarModalConfirmar = document.getElementById('cerrar-confirmar-modal-categoria');
                    let aceptarModalConfirmar = document.getElementById('aceptar-confirmar-modal-categoria');

                    aceptarModalConfirmar.addEventListener('click', () => {
                        fetch('/categoria/eliminar/' + rowData.id)
                        .then(response => response.json())
                        .then(data => {
                            if (data.response !== 'error') {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalExito = document.getElementById('mensaje-exito-modal-categoria');
                                mensajeModalExito.innerHTML = 'La categoría se eliminó correctamente.';
                                let cerrarModalExito = document.getElementById('cerrar-exito-modal-categoria');
                                cerrarModalExito.addEventListener('click', () => {
                                    modalExito.classList.add('hidden');
                                });
                                modalExito.classList.remove('hidden');
                            } else {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalError = document.getElementById('mensaje-error-modal-categoria');
                                mensajeModalError.innerHTML = data.error;
                                let cerrarModalError = document.getElementById('cerrar-error-modal-categoria');
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
            let cerrarModalError = document.getElementById('cerrar-error-modal-categoria');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEliminarCategoria.addEventListener('click', () => {
    modalEliminar.classList.add('hidden');
    table.destroy();
});

abrirEditarCategoriaTabla.addEventListener('click', () => {
    let datatableEditar = document.getElementById('editar-datatable-categorias');

    fetch('/categoria/obtener_todos')
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
                    { data: 'foto' }
                ]
            });

            modalEditarTabla.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black');
            datatableEditar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl');
            datatableEditar.children[3].remove();

            document.querySelectorAll('#editar-datatable-categorias tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    fetch('/categoria/obtener/' + rowData.id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.response !== 'error') {
                            modalEditarTabla.classList.add('hidden');
                            let fotoAnterior = document.getElementById('fotoAnterior');
                            let nombreEditar = document.getElementById('nombreEditar');

                            if (rowData.foto != null) {
                                fotoAnterior.src = '/uploads/categorias/' + rowData.foto;
                            } else {
                                fotoAnterior.src = '/static/images/null.png';
                            }
                            nombreEditar.value = rowData.nombre;

                            cerrarEditarCategoriaDatos.addEventListener('click', () => {
                                modalEditarDatos.classList.add('hidden');
                                modalEditarTabla.classList.remove('hidden');
                            })

                            let botonEditarCategoria = document.getElementById('boton-editar-categoria');

                            botonEditarCategoria.addEventListener('click', () => {
                                let fotoEditar = document.getElementById('fotoEditar').files[0];
                                let nombreEditar = document.getElementById('nombreEditar').value;

                                let datos = new FormData();
                                datos.append('nombre', nombreEditar);
                                datos.append('foto', fotoEditar);
                                datos.append('idAntiguo', rowData.id);

                                const options = {
                                    method: "POST",
                                    body: datos
                                };

                                fetch('/categoria/editar', options)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.response !== 'error') {
                                        modalEditarDatos.classList.add('hidden');
                                        modalConfirmar.classList.add('hidden');
                                        let mensajeModalExito = document.getElementById('mensaje-exito-modal-categoria');
                                        mensajeModalExito.innerHTML = 'La categoría se editó correctamente.';
                                        let cerrarModalExito = document.getElementById('cerrar-exito-modal-categoria');
                                        cerrarModalExito.addEventListener('click', () => {
                                            modalExito.classList.add('hidden');
                                        });
                                        modalExito.classList.remove('hidden');
                                    } else {
                                        modalEditarDatos.classList.add('hidden');
                                        let mensajeModalError = document.getElementById('mensaje-error-modal-categoria');
                                        mensajeModalError.innerHTML = data.error;
                                        let cerrarModalError = document.getElementById('cerrar-error-modal-categoria');
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
                            let mensajeModalError = document.getElementById('mensaje-error-modal-categoria');
                            mensajeModalError.innerHTML = data.error;
                            let cerrarModalError = document.getElementById('cerrar-error-modal-categoria');
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
            let cerrarModalError = document.getElementById('cerrar-error-modal-categoria');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEditarCategoriaTabla.addEventListener('click', () => {
    modalEditarTabla.classList.add('hidden');
    table.destroy();
});