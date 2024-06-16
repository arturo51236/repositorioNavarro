<?php

namespace App\Controller;

use App\Entity\LineaPedido;
use App\Entity\Pedido;
use App\Entity\Producto;
use App\Repository\LineaPedidoRepository;
use App\Repository\PedidoRepository;
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
#[Route('/lineapedido', requirements: ['_locale' => 'es'], name: 'lineapedido_')]
class LineaPedidoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductoRepository $productoRepository;
    private PedidoRepository $pedidoRepository;
    private LineaPedidoRepository $lineapedidoRepository;

    public function __construct(ProductoRepository $productoRepository, PedidoRepository $pedidoRepository, LineaPedidoRepository $lineapedidoRepository, EntityManagerInterface $entityManager)
    {
        $this->pedidoRepository = $pedidoRepository;
        $this->productoRepository = $productoRepository;
        $this->lineapedidoRepository = $lineapedidoRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/crear', name: 'crear', methods: ['POST'])]
    public function crear(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $cantidad = $request->request->get('cantidad');
        $uuidProducto = $request->request->get('producto');
        $idPedido = $request->request->get('pedido');
        $usuario = $this->getUser();

        $pedido = $this->pedidoRepository->findById($idPedido);

        if ($pedido != null) {
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
                $lineaPedido = new LineaPedido();
                $lineaPedido->setUuid(Uuid::v7());
                $lineaPedido->setCantidad($cantidad);
                $lineaPedido->setProducto($producto);
                $lineaPedido->setPedido($pedido[0]);

                try {
                    $this->entityManager->persist($lineaPedido);
                    $this->entityManager->flush();

                    $producto->setDisponibilidad($producto->getDisponibilidad() - $cantidad);

                    $this->entityManager->persist($producto);
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
    }
}
