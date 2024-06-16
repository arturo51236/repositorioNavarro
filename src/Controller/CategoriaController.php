<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/categoria', requirements: ['_locale' => 'es'], name: 'categoria_')]
class CategoriaController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CategoriaRepository $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository, EntityManagerInterface $entityManager)
    {
        $this->categoriaRepository = $categoriaRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/obtener_todos', name: 'obtener_todos')]
    public function obtener_categorias(SerializerInterface $serializer): JsonResponse
    {
        $categorias = $this->categoriaRepository->findAll();

        if ($categorias != null) {
            return $this->json($categorias);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron categorías.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/obtener/{id}', name: 'obtener')]
    public function obtener(SerializerInterface $serializer, int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categoria = $this->categoriaRepository->findById($id);

        if ($categoria != null) {
            return $this->json($categoria[0]);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontró la categoría.'],
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
        $foto = $request->files->get('foto');

        if ($nombre == null) {
            $errors[] = 'No puedes crear una categoría sin nombre.';
        }

        if (strlen($nombre) > 50) {
            $errors[] = 'No puedes superar los 50 caracteres.';
        }

        if ($foto !== null) {
            $maxFileSize = 6144 * 1024;
            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

            $fileMimeType = mime_content_type($foto->getRealPath());
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                $errors[] = 'Por favor, sube un fichero válido (JPG, JPEG o PNG).';
            }

            if ($foto->getSize() > $maxFileSize) {
                $errors[] = 'La foto introducida supera el límite de tamaño (6mb).';
            }
        }

        if (empty($errors)) {
            $categoria = new Categoria();
            $categoria->setUuid(Uuid::v7());
            $categoria->setNombre($nombre);

            if ($foto != null) {
                $fotoNuevoNombre = uniqid(true) . '.' . $foto->guessExtension();

                try {
                    $foto->move(
                        $this->getParameter('directorio_uploads_categorias'),
                        $fotoNuevoNombre
                    );
                } catch (FileException $ex) {
                    $this->addFlash('error', 'Error al guardar la foto de la categoría.');
    
                    return $this->redirectToRoute('admin_categorias');
                }
    
                $categoria->setFoto($fotoNuevoNombre);
            }

            try {
                $this->entityManager->persist($categoria);
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

        $categoria = $this->categoriaRepository->findById($id);

        if ($categoria != null) {
            try {
                $this->entityManager->remove($categoria[0]);
                $this->entityManager->flush();

                $filesystem = new Filesystem();
                $rutaFotoAntigua = $this->getParameter('directorio_uploads_categorias') . '/' . $categoria[0]->getFoto();

                if ($filesystem->exists($rutaFotoAntigua)) {
                    $filesystem->remove($rutaFotoAntigua);
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
                ['response' => 'error', 'error' => 'No se encontró la categoría.'],
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
        $categoria = $this->categoriaRepository->findById($idAntiguo);

        if ($categoria != null) {
            $fotoAntigua = $categoria[0]->getFoto();

            $this->entityManager->remove($categoria[0]);
            $this->entityManager->flush();

            $filesystem = new Filesystem();
            $rutaFotoAntigua = $this->getParameter('directorio_uploads_categorias') . '/' . $categoria[0]->getFoto();

            if ($filesystem->exists($rutaFotoAntigua)) {
                $filesystem->remove($rutaFotoAntigua);
            }

            $nombre = $request->request->get('nombre');
            $foto = $request->files->get('foto');

            if ($nombre == null) {
                $errors[] = 'No puedes editar la categoría sin especificar un nombre.';
            }

            if (strlen($nombre) > 50) {
                $errors[] = 'No puedes superar los 50 caracteres.';
            }

            if ($foto !== null) {
                $maxFileSize = 6144 * 1024;
                $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                $fileMimeType = mime_content_type($foto->getRealPath());
                if (!in_array($fileMimeType, $allowedMimeTypes)) {
                    $errors[] = 'Por favor, sube un fichero válido (JPG, JPEG o PNG).';
                }

                if ($foto->getSize() > $maxFileSize) {
                    $errors[] = 'La foto introducida supera el límite de tamaño (6mb).';
                }
            }

            if (empty($errors)) {
                $categoria = new Categoria();
                $categoria->setUuid(Uuid::v7());
                $categoria->setNombre($nombre);
    
                if ($foto != null) {
                    $fotoNuevoNombre = uniqid(true) . '.' . $foto->guessExtension();

                    try {
                        $foto->move(
                            $this->getParameter('directorio_uploads_categorias'),
                            $fotoNuevoNombre
                        );
                    } catch (FileException $ex) {
                        $this->addFlash('error', 'Error al guardar la foto de la categoría.');
        
                        return $this->redirectToRoute('admin_categorias');
                    }

                    $categoria->setFoto($fotoNuevoNombre);
                } else {
                    $categoria->setFoto($fotoAntigua);
                }

                try {
                    $this->entityManager->persist($categoria);
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
                ['response' => 'error', 'error' => 'No se encontró la categoría.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
