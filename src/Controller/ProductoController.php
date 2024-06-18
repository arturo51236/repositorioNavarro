<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use App\Repository\FabricanteRepository;
use App\Repository\MemoriaCestaRepository;
use App\Repository\LineaPedidoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/producto', requirements: ['_locale' => 'es'], name: 'producto_')]
class ProductoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductoRepository $productoRepository;
    private CategoriaRepository $categoriaRepository;
    private FabricanteRepository $fabricanteRepository;
    private MemoriaCestaRepository $memoriacestaRepository;
    private LineaPedidoRepository $lineapedidoRepository;

    public function __construct(ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository, FabricanteRepository $fabricanteRepository, MemoriaCestaRepository $memoriacestaRepository, LineaPedidoRepository $lineapedidoRepository, EntityManagerInterface $entityManager)
    {
        $this->productoRepository = $productoRepository;
        $this->categoriaRepository = $categoriaRepository;
        $this->fabricanteRepository = $fabricanteRepository;
        $this->memoriacestaRepository = $memoriacestaRepository;
        $this->lineapedidoRepository = $lineapedidoRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/obtener_todos', name: 'obtener_todos')]
    public function obtener_productos(SerializerInterface $serializer): JsonResponse
    {
        $productos = $this->productoRepository->findAll();

        if ($productos != null) {
            return $this->json($productos);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron productos.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/obtener_por_categoria/{uuid}', name: 'obtener_por_categoria')]
    public function obtener_productos_categoria(SerializerInterface $serializer, string $uuid): JsonResponse
    {
        $productos_categoria = $this->productoRepository->findByCategoria($uuid);

        if ($productos_categoria != null) {
            return $this->json($productos_categoria);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron productos con diseño propio.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/obtener_propios', name: 'obtener_propios')]
    public function obtener_productos_propios(SerializerInterface $serializer): JsonResponse
    {
        $productos_propios = $this->productoRepository->findByDiseno_propio();

        if ($productos_propios != null) {
            return $this->json($productos_propios);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron productos con diseño propio.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/obtener/{id}', name: 'obtener')]
    public function obtener(SerializerInterface $serializer, int $id): JsonResponse
    {
        $producto = $this->productoRepository->findById($id);

        if ($producto != null) {
            return $this->json($producto[0]);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontró el producto.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/obtener/{uuid}', name: 'obtener_uuid')]
    public function obtener_por_uuid(SerializerInterface $serializer, string $uuid): JsonResponse
    {
        $producto = $this->productoRepository->findByUuid($uuid);

        if ($producto != null) {
            return $this->json($producto);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontró el producto.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/crear', name: 'crear', methods: ['POST'])]
    public function crear(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $nombre = $request->request->get('nombre');
        $fotos = $request->files->all('fotos');
        $precio = $request->request->get('precio');
        $disponibilidad = $request->request->get('disponibilidad');
        $disenoPropio = filter_var($request->request->get('diseno_propio'), FILTER_VALIDATE_BOOLEAN);
        $categoria = $request->request->get('categoria');
        $fabricante = $request->request->get('fabricante');
        $descripcion = $request->request->get('descripcion');

        if ($nombre == null || $fotos == null || $precio == null || $disponibilidad == null || $categoria == null || $fabricante == null) {
            $errors[] = 'Te faltan campos por rellenar, el único campo optativo es la descripción.';
        }

        if (strlen($nombre) > 150) {
            $errors[] = 'No puedes superar los 150 caracteres en el nombre.';
        }

        if (strlen($descripcion) > 255) {
            $errors[] = 'No puedes superar los 255 caracteres en la descripción.';
        }

        if ($fotos !== null) {
            $maxFileSize = 6144 * 1024;
            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

            foreach ($fotos as $foto) {
                $fileMimeType = mime_content_type($foto->getRealPath());
                if (!in_array($fileMimeType, $allowedMimeTypes)) {
                    $errors[] = 'Por favor, sube un fichero válido (JPG, JPEG o PNG).';
                }
    
                if ($foto->getSize() > $maxFileSize) {
                    $errors[] = 'La foto introducida supera el límite de tamaño (6mb).';
                }
            }
        }

        if (empty($errors)) {
            $producto = new Producto();
            $producto->setUuid(Uuid::v7());
            $producto->setNombre($nombre);
            $producto->setPrecio($precio);
            $producto->setDisponibilidad($disponibilidad);
            $producto->setDisenoPropio($disenoPropio);
            $producto->setCategoria($this->categoriaRepository->findById($categoria)[0]);
            $producto->setFabricante($this->fabricanteRepository->findById($fabricante)[0]);
            $producto->setDescripcion($descripcion);

            if ($fotos != null) {
                $fotosNombres = array();

                foreach ($fotos as $foto) {
                    $fotoNuevoNombre = uniqid(true) . '.' . $foto->guessExtension();

                    try {
                        $fotosNombres[] = $fotoNuevoNombre;

                        $foto->move(
                            $this->getParameter('directorio_uploads_productos'),
                            $fotoNuevoNombre
                        );
                    } catch (FileException $ex) {
                        $this->addFlash('error', 'Error al guardar las fotos del producto.');
        
                        return $this->redirectToRoute('admin_productos');
                    }
                }

                $producto->setFotos($fotosNombres);
            }

            try {
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/eliminar/{id}', name: 'eliminar', methods: ['GET'])]
    public function eliminar(SerializerInterface $serializer, int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $producto = $this->productoRepository->findById($id);

        if ($producto != null) {
            try {
                $this->entityManager->remove($producto[0]);
                $this->entityManager->flush();

                $filesystem = new Filesystem();

                foreach ($producto[0]->getFotos() as $foto) {
                    $rutaFotoAntigua = $this->getParameter('directorio_uploads_productos') . '/' . $foto;

                    if ($filesystem->exists($rutaFotoAntigua)) {
                        $filesystem->remove($rutaFotoAntigua);
                    }
                }

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
                ['response' => 'error', 'error' => 'No se encontró el producto.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/editar', name: 'editar', methods: ['POST'])]
    public function editar(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $idAntiguo = $request->request->get('idAntiguo');
        $producto = $this->productoRepository->findById($idAntiguo);

        if ($producto != null) {
            $lineasMemoriaCesta = $this->memoriacestaRepository->findByIdProducto($producto[0]->getId());
            $lineasPedido = $this->lineapedidoRepository->findByIdProducto($producto[0]->getId());
            $fotosAntiguas = $producto[0]->getFotos();

            $fotos = $request->files->all('fotos');
            $nombre = $request->request->get('nombre');
            $precio = $request->request->get('precio');
            $disponibilidad = $request->request->get('disponibilidad');
            $disenoPropio = filter_var($request->request->get('diseno_propio'), FILTER_VALIDATE_BOOLEAN);
            $categoria = $request->request->get('categoria');
            $fabricante = $request->request->get('fabricante');
            $descripcion = $request->request->get('descripcion');

            if ($nombre == null || $precio == null || $disponibilidad == null || $categoria == null || $fabricante == null) {
                $errors[] = 'Te faltan campos por rellenar, el único campo optativo es la descripción.';
            }

            if (strlen($nombre) > 150) {
                $errors[] = 'No puedes superar los 150 caracteres en el nombre.';
            }

            if (strlen($descripcion) > 255) {
                $errors[] = 'No puedes superar los 255 caracteres en la descripción.';
            }

            if ($fotos !== null) {
                $maxFileSize = 6144 * 1024;
                $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                foreach ($fotos as $foto) {
                    $fileMimeType = mime_content_type($foto->getRealPath());
                    if (!in_array($fileMimeType, $allowedMimeTypes)) {
                        $errors[] = 'Por favor, sube un fichero válido (JPG, JPEG o PNG).';
                    }
        
                    if ($foto->getSize() > $maxFileSize) {
                        $errors[] = 'La foto introducida supera el límite de tamaño (6mb).';
                    }
                }
            }

            if (empty($errors)) {
                $productoNuevo = new Producto();
                $productoNuevo->setUuid(Uuid::v7());
                $productoNuevo->setNombre($nombre);
                $productoNuevo->setPrecio($precio);
                $productoNuevo->setDisponibilidad($disponibilidad);
                $productoNuevo->setDisenoPropio($disenoPropio);
                $productoNuevo->setCategoria($this->categoriaRepository->findById($categoria)[0]);
                $productoNuevo->setFabricante($this->fabricanteRepository->findById($fabricante)[0]);
                $productoNuevo->setDescripcion($descripcion);

                $filesystem = new Filesystem();
                if ($fotos != null) {
                    $fotosNombres = array();

                    foreach ($fotos as $foto) {
                        $fotoNuevoNombre = uniqid(true) . '.' . $foto->guessExtension();

                        try {
                            $fotosNombres[] = $fotoNuevoNombre;
    
                            $foto->move(
                                $this->getParameter('directorio_uploads_productos'),
                                $fotoNuevoNombre
                            );
                        } catch (FileException $ex) {
                            $this->addFlash('error', 'Error al guardar las fotos del producto.');
            
                            return $this->redirectToRoute('admin_productos');
                        }
                    }

                    foreach ($fotosAntiguas as $foto) {
                        $rutaFotoAntigua = $this->getParameter('directorio_uploads_productos') . '/' . $foto;
        
                        if ($filesystem->exists($rutaFotoAntigua)) {
                            $filesystem->remove($rutaFotoAntigua);
                        }
                    }

                    $productoNuevo->setFotos($fotosNombres);
                } else {
                    $productoNuevo->setFotos($fotosAntiguas);
                }

                try {
                    $this->entityManager->persist($productoNuevo);
                    $this->entityManager->flush();

                    if ($lineasMemoriaCesta != null) {
                        foreach ($lineasMemoriaCesta as $lineaM) {
                            $lineaM->setProducto($productoNuevo);
                            $this->entityManager->persist($lineaM);
                        }
                    }

                    if ($lineasPedido != null) {
                        foreach ($lineasPedido as $lineaP) {
                            $lineaP->setProducto($productoNuevo);
                            $this->entityManager->persist($lineaP);
                        }
                    }

                    $this->entityManager->flush();

                    $this->entityManager->remove($producto[0]);
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
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontró el producto.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
