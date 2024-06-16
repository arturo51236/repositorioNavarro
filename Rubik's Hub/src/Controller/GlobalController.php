<?php

namespace App\Controller;

use App\Controller\ProductoController;
use App\Controller\FabricanteController;
use App\Controller\MemoriaCestaController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GlobalController extends AbstractController
{
    private ProductoController $productoController;
    private FabricanteController $fabricanteController;
    private MemoriaCestaController $memoriacestaController;

    public function __construct(ProductoController $productoController, FabricanteController $fabricanteController, MemoriaCestaController $memoriacestaController)
    {
        $this->productoController = $productoController;
        $this->fabricanteController = $fabricanteController;
        $this->memoriacestaController = $memoriacestaController;
    }

    #[Route('/', name: 'global_/')]
    public function index(SerializerInterface $serializer): Response
    {
        $productos_respuesta = $this->productoController->obtener_productos($serializer);

        $productos = json_decode($productos_respuesta->getContent(), true);

        return $this->render('global/index.html.twig', [
            'controller_name' => 'GlobalController',
            'productos' => $productos,
        ]);
    }

    #[Route('/global/producto/{uuid}', name: 'global_producto')]
    public function global_producto(SerializerInterface $serializer, string $uuid): Response
    {
        $producto_respuesta = $this->productoController->obtener_por_uuid($serializer, $uuid);

        $producto = json_decode($producto_respuesta->getContent(), true);

        return $this->render('global/producto.html.twig', [
            'controller_name' => 'GlobalController',
            'producto' => $producto,
        ]);
    }

    #[Route('/global/productos/categoria/{uuid}', name: 'global_productos_categoria')]
    public function global_productos_categoria(SerializerInterface $serializer, string $uuid): Response
    {
        $productos_categoria_respuesta = $this->productoController->obtener_productos_categoria($serializer, $uuid);

        $productos_categoria = json_decode($productos_categoria_respuesta->getContent(), true);

        return $this->render('global/productos_categoria.html.twig', [
            'controller_name' => 'GlobalController',
            'productos_categoria' => $productos_categoria,
        ]);
    }

    #[Route('/global/productos_propios', name: 'global_productos_propios')]
    public function global_productos_propios(SerializerInterface $serializer): Response
    {
        $productos_propios_respuesta = $this->productoController->obtener_productos_propios($serializer);

        $productos_propios = json_decode($productos_propios_respuesta->getContent(), true);

        return $this->render('global/productos_propios.html.twig', [
            'controller_name' => 'GlobalController',
            'productos_propios' => $productos_propios,
        ]);
    }

    #[Route('/global/sobre_nosotros', name: 'global_sobre_nosotros')]
    public function global_sobre_nosotros(SerializerInterface $serializer): Response
    {
        return $this->render('global/sobre_nosotros.html.twig', [
            'controller_name' => 'GlobalController',
        ]);
    }

    #[Route('/global/fabricantes', name: 'global_fabricantes')]
    public function global_fabricantes(SerializerInterface $serializer): Response
    {
        $fabricantes_respuesta = $this->fabricanteController->obtener_fabricantes($serializer);

        $fabricantes = json_decode($fabricantes_respuesta->getContent(), true);

        return $this->render('global/fabricantes.html.twig', [
            'controller_name' => 'GlobalController',
            'fabricantes' => $fabricantes,
        ]);
    }

    #[Route('/global/carrito/usuario/{uuid}', name: 'global_carrito_usuario')]
    public function global_carrito_usuario(SerializerInterface $serializer, string $uuid): Response
    {
        $carrito_usuario_respuesta = $this->memoriacestaController->obtener_por_usuario($serializer, $uuid);

        $carrito_usuario = json_decode($carrito_usuario_respuesta->getContent(), true);

        return $this->render('global/carrito_usuario.html.twig', [
            'controller_name' => 'GlobalController',
            'carrito_usuario' => $carrito_usuario,
        ]);
    }
}
