<?php

namespace App\Controller;

use App\Repository\AccesoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route('/acceso', requirements: ['_locale' => 'es'], name: 'acceso_')]
class AccesoController extends AbstractController
{
    private AccesoRepository $accesoRepository;

    public function __construct(AccesoRepository $accesoRepository)
    {
        $this->accesoRepository = $accesoRepository;
    }

    #[Route('/obtener_todos', name: 'obtener_todos')]
    public function obtener_accesos(SerializerInterface $serializer): JsonResponse
    {
        $accesos = $this->accesoRepository->findAll();

        if ($accesos != null) {
            return $this->json($accesos);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron accesos'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
