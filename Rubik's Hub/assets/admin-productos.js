import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let modalCrear = document.getElementById('crear-modal-producto');
let modalEliminar = document.getElementById('eliminar-modal-producto');
let modalEditarTabla = document.getElementById('editar-modal-producto-tabla');
let modalEditarDatos = document.getElementById('editar-modal-producto-datos');
let abrirCrearProducto = document.getElementById('abrir-modal-crear-producto');
let cerrarCrearProducto = document.getElementById('cerrar-modal-crear-producto');
let abrirEliminarProducto = document.getElementById('abrir-modal-eliminar-producto');
let cerrarEliminarProducto = document.getElementById('cerrar-modal-eliminar-producto');
let abrirEditarProductoTabla = document.getElementById('abrir-modal-editar-producto-tabla');
let cerrarEditarProductoTabla = document.getElementById('cerrar-modal-editar-producto-tabla');
let cerrarEditarProductoDatos = document.getElementById('cerrar-modal-editar-producto-datos');

let modalExito = document.getElementById('exito-modal-producto');
let modalError = document.getElementById('error-modal-producto');
let modalConfirmar = document.getElementById('confirmar-modal-producto');

let table;

abrirCrearProducto.addEventListener('click', async () => {
    const categoriasCargadas = await obtenerYPintarCategorias('categoriaCrear');
    const fabricantesCargados = await obtenerYPintarFabricantes('fabricanteCrear');

    if (categoriasCargadas && fabricantesCargados) {
        modalCrear.classList.remove('hidden');

        let botonCrearProducto = document.getElementById('boton-crear-producto');

        botonCrearProducto.addEventListener('click', () => {
            let fotosCrear = document.getElementById('fotosCrear').files;
            let nombreCrear = document.getElementById('nombreCrear').value;
            let precioCrear = document.getElementById('precioCrear').value;
            let disponibilidadCrear = document.getElementById('disponibilidadCrear').value;
            let disenoPropioCrear = document.getElementById('disenoPropioCrear').checked;
            let categoriaCrear = document.getElementById('categoriaCrear').value;
            let fabricanteCrear = document.getElementById('fabricanteCrear').value;
            let descripcionCrear = document.getElementById('descripcionCrear').value;
    
            let datos = new FormData();
            datos.append('nombre', nombreCrear);
            for (let i = 0; i < fotosCrear.length; i++) {
                datos.append('fotos[]', fotosCrear[i]);
            }
            datos.append('precio', precioCrear);
            datos.append('disponibilidad', disponibilidadCrear);
            datos.append('diseno_propio', disenoPropioCrear);
            datos.append('categoria', categoriaCrear);
            datos.append('fabricante', fabricanteCrear);
            datos.append('descripcion', descripcionCrear);
    
            const options = {
                method: "POST",
                body: datos
            };
    
            fetch('/producto/crear', options)
            .then(response => response.json())
            .then(data => {
                if (data.response !== 'error') {
                    modalCrear.classList.add('hidden');
                    let mensajeModalExito = document.getElementById('mensaje-exito-modal-producto');
                    mensajeModalExito.innerHTML = 'El producto se creó correctamente.';
                    let cerrarModalExito = document.getElementById('cerrar-exito-modal-producto');
                    cerrarModalExito.addEventListener('click', () => {
                        modalExito.classList.add('hidden');
                    });
                    modalExito.classList.remove('hidden');
                } else {
                    modalCrear.classList.add('hidden');
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
        }, { once: true });
    }
});

cerrarCrearProducto.addEventListener('click', () => {
    modalCrear.classList.add('hidden');
});

abrirEliminarProducto.addEventListener('click', () => {
    let datatableEliminar = document.getElementById('eliminar-datatable-productos');

    fetch('/producto/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            const transformedData = data.map(item => {
                return {
                    ...item,
                    descripcion: item.descripcion ? item.descripcion : 'Sin descripción',
                    categoria: item.categoria.nombre,
                    fabricante: item.fabricante.nombre,
                    disenoPropio: item.disenoPropio ? 'Sí' : 'No'
                };
            });

            table = new DataTable(datatableEliminar, {
                bLengthChange: false,
                data: transformedData,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'precio' },
                    { data: 'descripcion' },
                    { data: 'disponibilidad' },
                    { data: 'disenoPropio' },
                    { data: 'fabricante' },
                    { data: 'categoria' }
                ]
            });

            modalEliminar.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black');
            datatableEliminar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl');
            datatableEliminar.children[3].remove();

            document.querySelectorAll('#eliminar-datatable-productos tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    modalEliminar.classList.add('hidden');
                    modalConfirmar.classList.remove('hidden');
                    let mensajeModalConfirmar = document.getElementById('mensaje-confirmar-modal-producto');
                    mensajeModalConfirmar.innerHTML = '¿Estás seguro de que deseas eliminar a el producto ' + rowData.nombre + '?';
                    let cerrarModalConfirmar = document.getElementById('cerrar-confirmar-modal-producto');
                    let aceptarModalConfirmar = document.getElementById('aceptar-confirmar-modal-producto');

                    aceptarModalConfirmar.addEventListener('click', () => {
                        fetch('/producto/eliminar/' + rowData.id)
                        .then(response => response.json())
                        .then(data => {
                            if (data.response !== 'error') {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalExito = document.getElementById('mensaje-exito-modal-producto');
                                mensajeModalExito.innerHTML = 'El producto se eliminó correctamente.';
                                let cerrarModalExito = document.getElementById('cerrar-exito-modal-producto');
                                cerrarModalExito.addEventListener('click', () => {
                                    modalExito.classList.add('hidden');
                                });
                                modalExito.classList.remove('hidden');
                            } else {
                                modalConfirmar.classList.add('hidden');
                                let mensajeModalError = document.getElementById('mensaje-error-modal-producto');
                                mensajeModalError.innerHTML = data.error;
                                let cerrarModalError = document.getElementById('cerrar-error-modal-producto');
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
            let cerrarModalError = document.getElementById('cerrar-error-modal-producto');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEliminarProducto.addEventListener('click', () => {
    modalEliminar.classList.add('hidden');
    table.destroy();
});

abrirEditarProductoTabla.addEventListener('click', () => {
    let datatableEditar = document.getElementById('editar-datatable-productos');

    fetch('/producto/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            const transformedData = data.map(item => {
                return {
                    ...item,
                    descripcion: item.descripcion ? item.descripcion : 'Sin descripción',
                    categoria: item.categoria.nombre,
                    fabricante: item.fabricante.nombre,
                    disenoPropio: item.disenoPropio ? 'Sí' : 'No'
                };
            });

            table = new DataTable(datatableEditar, {
                bLengthChange: false,
                data: transformedData,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'precio' },
                    { data: 'descripcion' },
                    { data: 'disponibilidad' },
                    { data: 'disenoPropio' },
                    { data: 'fabricante' },
                    { data: 'categoria' }
                ]
            });

            modalEditarTabla.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-black');
            datatableEditar.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl');
            datatableEditar.children[3].remove();

            document.querySelectorAll('#editar-datatable-productos tbody tr').forEach((row, index) => {
                row.addEventListener('click', () => {
                    let rowData = table.row(row).data();
                    fetch('/producto/obtener/' + rowData.id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.response !== 'error') {
                            modalEditarTabla.classList.add('hidden');

                            let nombreEditar = document.getElementById('nombreEditar');
                            let precioEditar = document.getElementById('precioEditar');
                            let disponibilidadEditar = document.getElementById('disponibilidadEditar');
                            let disenoPropioEditar = document.getElementById('disenoPropioEditar');
                            let categoriaEditar = document.getElementById('categoriaEditar');
                            let fabricanteEditar = document.getElementById('fabricanteEditar');
                            let descripcionEditar = document.getElementById('descripcionEditar');

                            const categoriasCargadas = obtenerYPintarCategorias('categoriaEditar');
                            const fabricantesCargados = obtenerYPintarFabricantes('fabricanteEditar');

                            if (categoriasCargadas && fabricantesCargados) {
                                nombreEditar.value = rowData.nombre;
                                precioEditar.value = rowData.precio;
                                disponibilidadEditar.value = rowData.disponibilidad;
                                disenoPropioEditar.checked = rowData.disenoPropio;
                                categoriaEditar.value = rowData.categoria;
                                fabricanteEditar.value = rowData.fabricante;
                                descripcionEditar.value = rowData.descripcion;

                                cerrarEditarProductoDatos.addEventListener('click', () => {
                                    modalEditarDatos.classList.add('hidden');
                                    modalEditarTabla.classList.remove('hidden');
                                })

                                let botonEditarproducto = document.getElementById('boton-editar-producto');

                                botonEditarproducto.addEventListener('click', () => {
                                    let fotosEditar = document.getElementById('fotosEditar').files;
                                    let nombreEditar = document.getElementById('nombreEditar').value;
                                    let precioEditar = document.getElementById('precioEditar').value;
                                    let disponibilidadEditar = document.getElementById('disponibilidadEditar').value;
                                    let disenoPropioEditar = document.getElementById('disenoPropioEditar').checked;
                                    let categoriaEditar = document.getElementById('categoriaEditar').value;
                                    let fabricanteEditar = document.getElementById('fabricanteEditar').value;
                                    let descripcionEditar = document.getElementById('descripcionEditar').value;

                                    let datos = new FormData();
                                    datos.append('nombre', nombreEditar);
                                    for (let i = 0; i < fotosEditar.length; i++) {
                                        datos.append('fotos[]', fotosEditar[i]);
                                    }
                                    datos.append('precio', precioEditar);
                                    datos.append('disponibilidad', disponibilidadEditar);
                                    datos.append('diseno_propio', disenoPropioEditar);
                                    datos.append('categoria', categoriaEditar);
                                    datos.append('fabricante', fabricanteEditar);
                                    datos.append('descripcion', descripcionEditar);
                                    datos.append('idAntiguo', rowData.id);

                                    const options = {
                                        method: "POST",
                                        body: datos
                                    };

                                    fetch('/producto/editar', options)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.response !== 'error') {
                                            modalEditarDatos.classList.add('hidden');
                                            modalConfirmar.classList.add('hidden');
                                            let mensajeModalExito = document.getElementById('mensaje-exito-modal-producto');
                                            mensajeModalExito.innerHTML = 'El producto se editó correctamente.';
                                            let cerrarModalExito = document.getElementById('cerrar-exito-modal-producto');
                                            cerrarModalExito.addEventListener('click', () => {
                                                modalExito.classList.add('hidden');
                                            });
                                            modalExito.classList.remove('hidden');
                                        } else {
                                            modalEditarDatos.classList.add('hidden');
                                            let mensajeModalError = document.getElementById('mensaje-error-modal-producto');
                                            mensajeModalError.innerHTML = data.error;
                                            let cerrarModalError = document.getElementById('cerrar-error-modal-producto');
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
                            modalEditarTabla.classList.add('hidden');
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
                }, { once: true })
            })

            datatableEditar.classList.remove('hidden');
            modalEditarTabla.classList.remove('hidden');
        } else {
            modalError.classList.remove('hidden');
            let cerrarModalError = document.getElementById('cerrar-error-modal-producto');
            cerrarModalError.addEventListener('click', () => {
                modalError.classList.add('hidden');
            });
        }
    })
    .catch(error => console.log(error));
});

cerrarEditarProductoTabla.addEventListener('click', () => {
    modalEditarTabla.classList.add('hidden');
    table.destroy();
});

async function obtenerYPintarCategorias(id) {
    try {
        let response = await fetch('/categoria/obtener_todos');
        let data = await response.json();

        if (data.response !== 'error') {
            let categoriaInput = document.getElementById(id);

            data.forEach((categoria) => {
                let option = document.createElement('option');
                option.innerHTML = categoria.nombre;
                option.value = categoria.id;
                categoriaInput.appendChild(option);
            });

            return true;
        } else {
            let mensajeModalError = document.getElementById('mensaje-error-modal-producto');
            mensajeModalError.innerHTML = data.error;
            let cerrarModalError = document.getElementById('cerrar-error-modal-producto');
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

async function obtenerYPintarFabricantes(id) {
    try {
        let response = await fetch('/fabricante/obtener_todos');
        let data = await response.json();

        if (data.response !== 'error') {
            let fabricanteInput = document.getElementById(id);

            data.forEach((fabricante) => {
                let option = document.createElement('option');
                option.innerHTML = fabricante.nombre;
                option.value = fabricante.id;
                fabricanteInput.appendChild(option);
            });

            return true;
        } else {
            let mensajeModalError = document.getElementById('mensaje-error-modal-producto');
            mensajeModalError.innerHTML = data.error;
            let cerrarModalError = document.getElementById('cerrar-error-modal-producto');
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