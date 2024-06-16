import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let mid = document.getElementById('mid');
let datatable = document.getElementById('datatable-acceso');

document.addEventListener("DOMContentLoaded", function(event) {
    fetch('/acceso/obtener_todos')
    .then(response => response.json())
    .then(data => {
        if (data.response !== 'error') {
            const transformedData = data.map(item => {
                return {
                    ...item,
                    usuario: item.usuario != null ? item.usuario.email : 'Desconocido'
                };
            });

            let table = new DataTable(datatable, {
                bLengthChange: false,
                data: transformedData,
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'fecha' },
                    { data: 'resultado' },
                    { data: 'usuario' }
                ]
            });

            mid.children[0].classList.add('m-auto', 'bg-indigo-400', 'p-6', 'rounded-3xl', 'border-4', 'border-black', 'text-white');
            datatable.classList.remove('hidden');
            document.getElementById('volver-atras').classList.remove('hidden');
            datatable.classList.add('bg-white', 'p-5', 'm-5', 'text-black', 'rounded-xl', 'w-full');
            datatable.children[3].remove();
        } else {
            let modalError = document.getElementById('error-modal-acceso');
            modalError.classList.remove('hidden');
            let cerrarModalError = document.getElementById('cerrar-error-modal-acceso');
            cerrarModalError.addEventListener('click', () => {
                window.history.back();
            });
        }
    })
    .catch(error => console.log(error));
});