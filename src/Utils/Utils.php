<?php

namespace App\Utils;

class Utils
{
    public static function validar_dni($dni): bool
    {
        $dni = strtoupper($dni);
        $regex = '/^[0-9]{8}[A-Z]$/';
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";

        if (preg_match($regex, $dni)) {
            return ($letras[(substr($dni, 0, 8) % 23)] == $dni[8]);
        } else {
            return false;
        }
    }

    public static function validar_nombre_apellidos($texto): bool
    {
        $regex = '/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/';

        if (preg_match($regex, $texto) || empty($texto)) {
            return true;
        } else {
            return false;
        }
    }
}