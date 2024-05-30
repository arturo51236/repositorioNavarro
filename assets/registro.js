import './lib/select2.js';
import './vendor/select2/dist/css/select2.min.css';

$(function() {
    $('#selector-pais').select2({
        placeholder: 'Selecciona un país'
    });
    $('#selector-provincia').select2({
        placeholder: 'Selecciona una provincia'
    });
});