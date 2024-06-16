<?php

namespace App\Controller;

use App\Entity\Estado;
use App\Repository\EstadoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[IsGranted('ROLE_ADMIN')]
#[Route('/estado', requirements: ['_locale' => 'es'], name: 'estado_')]
class EstadoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EstadoRepository $estadoRepository;

    public function __construct(EstadoRepository $estadoRepository, EntityManagerInterface $entityManager)
    {
        $this->estadoRepository = $estadoRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/obtener_todos', name: 'obtener_todos')]
    public function obtener_estados(SerializerInterface $serializer): JsonResponse
    {
        $estados = $this->estadoRepository->findAll();

        if ($estados != null) {
            return $this->json($estados);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron estados.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/crear', name: 'crear', methods: ['POST'])]
    public function crear(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $nombre = $request->request->get('nombre');

        if ($nombre == null) {
            $errors[] = 'No puedes crear un estado sin nombre.';
        }

        if (strlen($nombre) > 50) {
            $errors[] = 'No puedes superar los 50 caracteres.';
        }

        if (empty($errors)) {
            $estado = new Estado();
            $estado->setUuid(Uuid::v7());
            $estado->setNombre($nombre);

            try {
                $this->entityManager->persist($estado);
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
                ['response' => 'error', 'error' => $errors[0]],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/eliminar/{id}', name: 'eliminar', methods: ['GET'])]
    public function eliminar(SerializerInterface $serializer, int $id): JsonResponse
    {
        $estado = $this->estadoRepository->findById($id);

        if ($estado != null) {
            try {
                $this->entityManager->remove($estado[0]);
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
                ['response' => 'error', 'error' => 'No se encontró el estado.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
