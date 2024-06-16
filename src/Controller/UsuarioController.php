<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[IsGranted('ROLE_ADMIN')]
#[Route('/usuario', requirements: ['_locale' => 'es'], name: 'usuario_')]
class UsuarioController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository, EntityManagerInterface $entityManager)
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/obtener_todos', name: 'obtener_todos')]
    public function obtener_usuarios(SerializerInterface $serializer): JsonResponse
    {
        $usuarios = $this->usuarioRepository->findAll();

        if ($usuarios != null) {
            return $this->json($usuarios);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron usuarios.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/eliminar/{id}', name: 'eliminar', methods: ['GET'])]
    public function eliminar(SerializerInterface $serializer, int $id): JsonResponse
    {
        $usuario = $this->usuarioRepository->findById($id);

        if ($usuario != null) {
            try {
                $this->entityManager->remove($usuario[0]);
                $this->entityManager->flush();
    
                return $this->json(
                    ['response' => 'ok'],
                    JsonResponse::HTTP_OK
                );
            } catch (Exception $exception) {
                return $this->json(
                    ['response' => 'error', 'error' => $exception->getMessage()],
                    JsonResponse::HTTP_NOT_FOUND
                );
            }
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontró el usuario.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
