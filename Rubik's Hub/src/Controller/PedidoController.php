<?php

namespace App\Controller;

use \DateTime;
use App\Entity\Pedido;
use App\Repository\PedidoRepository;
use App\Repository\EstadoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[IsGranted('ROLE_USER')]
#[Route('/pedido', requirements: ['_locale' => 'es'], name: 'pedido_')]
class PedidoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PedidoRepository $pedidoRepository;
    private EstadoRepository $estadoRepository;

    public function __construct(PedidoRepository $pedidoRepository, EstadoRepository $estadoRepository, EntityManagerInterface $entityManager)
    {
        $this->pedidoRepository = $pedidoRepository;
        $this->estadoRepository = $estadoRepository;
        $this->entityManager = $entityManager;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/obtener_todos', name: 'obtener_todos')]
    public function obtener_pedidos(SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pedidos = $this->pedidoRepository->findAll();

        if ($pedidos != null) {
            return $this->json($pedidos);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron pedidos.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/obtener/{id}', name: 'obtener')]
    public function obtener(SerializerInterface $serializer, int $id): JsonResponse
    {
        $pedido = $this->pedidoRepository->findById($id);

        if ($pedido != null) {
            return $this->json($pedido);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se ha encontrado el pedido.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/crear/{uuid}', name: 'crear')]
    public function crear(SerializerInterface $serializer, Request $request, string $uuid): JsonResponse
    {
        $usuario = $this->getUser();

        if ($usuario->getUuid() == $uuid) {
            $estado = $this->estadoRepository->findByNombre('No confirmado');

            $pedido = new Pedido();
            $pedido->setUuid(Uuid::v7());
            $pedido->setUsuario($usuario);
            $pedido->setFechaCreacion(new DateTime('now'));
            $pedido->setEstado($estado);

            try {
                $this->entityManager->persist($pedido);
                $this->entityManager->flush();

                return $this->json(
                    ['response' => 'ok', 'id' => $pedido->getId()],
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
                ['response' => 'error', 'error' => 'Error al crear el pedido, hubo un problema identificando tu usuario.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/editar', name: 'editar', methods: ['POST'])]
    public function editar(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $idEstado = $request->request->get('estado');
        $idPedido = $request->request->get('id');

        $pedido = $this->pedidoRepository->findById($idPedido);

        if ($pedido != null) {
            $estado = $this->estadoRepository->findById($idEstado);

            $pedido[0]->setEstado($estado[0]);

            try {
                $this->entityManager->persist($pedido[0]);
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
                ['response' => 'error', 'error' => 'No se ha encontrado el pedido.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
