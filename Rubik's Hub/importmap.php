<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'registro' => [
        'path' => './assets/registro.js',
        'entrypoint' => true,
    ],
    'global' => [
        'path' => './assets/global.js',
        'entrypoint' => true,
    ],
    'alpine' => [
        'path' => './assets/alpine.js',
        'entrypoint' => true,
    ],
    'global-carrito_usuario' => [
        'path' => './assets/global-carrito_usuario.js',
        'entrypoint' => true,
    ],
    'global-producto' => [
        'path' => './assets/global-producto.js',
        'entrypoint' => true,
    ],
    'global-productos_propios' => [
        'path' => './assets/global-productos_propios.js',
        'entrypoint' => true,
    ],
    'global-productos_categoria' => [
        'path' => './assets/global-productos_categoria.js',
        'entrypoint' => true,
    ],
    'admin-accesos' => [
        'path' => './assets/admin-accesos.js',
        'entrypoint' => true,
    ],
    'admin-categorias' => [
        'path' => './assets/admin-categorias.js',
        'entrypoint' => true,
    ],
    'admin-contacto' => [
        'path' => './assets/admin-contacto.js',
        'entrypoint' => true,
    ],
    'admin-estados' => [
        'path' => './assets/admin-estados.js',
        'entrypoint' => true,
    ],
    'admin-fabricantes' => [
        'path' => './assets/admin-fabricantes.js',
        'entrypoint' => true,
    ],
    'admin-pedidos' => [
        'path' => './assets/admin-pedidos.js',
        'entrypoint' => true,
    ],
    'admin-productos' => [
        'path' => './assets/admin-productos.js',
        'entrypoint' => true,
    ],
    'admin-usuarios' => [
        'path' => './assets/admin-usuarios.js',
        'entrypoint' => true,
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'select2' => [
        'version' => '4.1.0-rc.0',
    ],
    'select2/dist/css/select2.min.css' => [
        'version' => '4.1.0-rc.0',
        'type' => 'css',
    ],
    'tailwind' => [
        'version' => '4.0.0',
    ],
    'flowbite' => [
        'version' => '2.3.0',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'flowbite/dist/flowbite.min.css' => [
        'version' => '2.3.0',
        'type' => 'css',
    ],
    'datatables.net-dt' => [
        'version' => '2.0.8',
    ],
    'datatables.net' => [
        'version' => '2.0.8',
    ],
    'datatables.net-dt/css/dataTables.dataTables.min.css' => [
        'version' => '2.0.8',
        'type' => 'css',
    ],
    'alpinejs' => [
        'version' => '3.14.0',
    ],
];
