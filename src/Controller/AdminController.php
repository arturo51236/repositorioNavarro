<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', requirements: ['_locale' => 'es'], name: 'admin_')]
class AdminController extends AbstractController
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $opciones = [
            ['nombre' => 'Accesos', 'foto' => 'accesos.svg'],
            ['nombre' => 'Categorías', 'foto' => 'categorias.svg'],
            ['nombre' => 'Fabricantes', 'foto' => 'fabricantes.svg'],
            ['nombre' => 'Pedidos', 'foto' => 'pedidos.svg'],
            ['nombre' => 'Productos', 'foto' => 'productos.svg'],
            ['nombre' => 'Usuarios', 'foto' => 'usuarios.svg'],
            ['nombre' => 'Estados', 'foto' => 'estados.svg'],
            ['nombre' => 'Contacto', 'foto' => 'contacto.svg'],
        ];

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'opciones' => $opciones
        ]);
    }

    #[Route('/accesos', name: 'accesos')]
    public function accesos(): Response
    {
        return $this->render('admin/accesos.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/categorias', name: 'categorias')]
    public function categorias(): Response
    {
        return $this->render('admin/categorias.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/fabricantes', name: 'fabricantes')]
    public function fabricantes(): Response
    {
        return $this->render('admin/fabricantes.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/pedidos', name: 'pedidos')]
    public function pedidos(): Response
    {
        return $this->render('admin/pedidos.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/productos', name: 'productos')]
    public function productos(): Response
    {
        return $this->render('admin/productos.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/usuarios', name: 'usuarios')]
    public function usuarios(): Response
    {
        return $this->render('admin/usuarios.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/estados', name: 'estados')]
    public function estados(): Response
    {
        return $this->render('admin/estados.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/contacto', name: 'contacto')]
    public function contacto(): Response
    {
        return $this->render('admin/contacto.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
