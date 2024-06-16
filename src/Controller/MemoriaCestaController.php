<?php

namespace App\Controller;

use App\Entity\MemoriaCesta;
use App\Entity\Producto;
use App\Repository\MemoriaCestaRepository;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[IsGranted('ROLE_USER')]
#[Route('/memoriacesta', requirements: ['_locale' => 'es'], name: 'memoriacesta_')]
class MemoriaCestaController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductoRepository $productoRepository;
    private MemoriaCestaRepository $memoriacestaRepository;

    public function __construct(ProductoRepository $productoRepository, MemoriaCestaRepository $memoriacestaRepository, EntityManagerInterface $entityManager)
    {
        $this->productoRepository = $productoRepository;
        $this->memoriacestaRepository = $memoriacestaRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/obtener/usuario/{uuid}', name: 'obtener_usuario')]
    public function obtener_por_usuario(SerializerInterface $serializer, string $uuid): JsonResponse
    {
        $carritoUsuario = $this->memoriacestaRepository->findByUuidUsuario($uuid);

        if ($carritoUsuario != null) {
            return $this->json($carritoUsuario);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'Tu carrito está vacío.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/crear', name: 'crear', methods: ['POST'])]
    public function crear(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $cantidad = $request->request->get('cantidad');
        $uuidProducto = $request->request->get('producto');
        $usuario = $this->getUser();

        $producto = $this->productoRepository->findByUuid($uuidProducto);

        if ($usuario == null) {
            $errors[] = 'No puedes añadir un producto al carrito sin iniciar sesión.';
        }

        if ($cantidad == null || $cantidad == 0) {
            $errors[] = 'No se especificó cantidad o es una cantidad no permitida.';
        }

        if ($producto == null) {
            $errors[] = 'El producto no se pudo encontrar.';
        }

        if ($producto->getDisponibilidad() < $cantidad) {
            $errors[] = 'No quedan suficientes existencias de este producto.';
        }

        if (empty($errors)) {
            $memoriaCesta = new MemoriaCesta();
            $memoriaCesta->setUuid(Uuid::v7());
            $memoriaCesta->setCantidad($cantidad);
            $memoriaCesta->setProducto($producto);
            $memoriaCesta->setUsuario($usuario);

            try {
                $this->entityManager->persist($memoriaCesta);
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

    #[Route('/eliminar/{uuid}', name: 'eliminar', methods: ['GET'])]
    public function eliminar(SerializerInterface $serializer, string $uuid): JsonResponse
    {
        $memoriaCesta = $this->memoriacestaRepository->findByUuid($uuid);

        if ($memoriaCesta != null) {
            try {
                $this->entityManager->remove($memoriaCesta);
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
                ['response' => 'error', 'error' => 'No se encontró la categoría.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
