<?php

namespace App\Controller;

use App\Entity\Fabricante;
use App\Repository\FabricanteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/fabricante', requirements: ['_locale' => 'es'], name: 'fabricante_')]
class FabricanteController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FabricanteRepository $fabricanteRepository;

    public function __construct(FabricanteRepository $fabricanteRepository, EntityManagerInterface $entityManager)
    {
        $this->fabricanteRepository = $fabricanteRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/obtener_todos', name: 'obtener_todos')]
    public function obtener_fabricantes(SerializerInterface $serializer): JsonResponse
    {
        $fabricantes = $this->fabricanteRepository->findAll();

        if ($fabricantes != null) {
            return $this->json($fabricantes);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontraron fabricantes.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/obtener/{id}', name: 'obtener')]
    public function obtener(SerializerInterface $serializer, int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $fabricante = $this->fabricanteRepository->findById($id);

        if ($fabricante != null) {
            return $this->json($fabricante[0]);
        } else {
            return $this->json(
                ['response' => 'error', 'error' => 'No se encontró el fabricante.'],
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
        $descripcion = $request->request->get('descripcion');
        $foto = $request->files->get('foto');

        if ($nombre == null || $descripcion == null || $nombre == null && $descripcion == null) {
            $errors[] = 'No puedes crear un fabricante sin nombre y/o descripción.';
        }

        if (strlen($nombre) > 50) {
            $errors[] = 'No puedes superar los 50 caracteres.';
        }

        if (strlen($descripcion) > 255) {
            $errors[] = 'No puedes superar los 255 caracteres en la descripción.';
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
            $fabricante = new Fabricante();
            $fabricante->setUuid(Uuid::v7());
            $fabricante->setNombre($nombre);
            $fabricante->setDescripcion($descripcion);

            if ($foto != null) {
                $fotoNuevoNombre = uniqid(true) . '.' . $foto->guessExtension();

                try {
                    $foto->move(
                        $this->getParameter('directorio_uploads_fabricantes'),
                        $fotoNuevoNombre
                    );
                } catch (FileException $ex) {
                    $this->addFlash('error', 'Error al guardar la foto del fabricante.');
    
                    return $this->redirectToRoute('admin_fabricantes');
                }
    
                $fabricante->setFoto($fotoNuevoNombre);
            }

            try {
                $this->entityManager->persist($fabricante);
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

        $fabricante = $this->fabricanteRepository->findById($id);

        if ($fabricante != null) {
            try {
                $this->entityManager->remove($fabricante[0]);
                $this->entityManager->flush();

                $filesystem = new Filesystem();
                $rutaFotoAntigua = $this->getParameter('directorio_uploads_fabricantes') . '/' . $fabricante[0]->getFoto();

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
                ['response' => 'error', 'error' => 'No se encontró el fabricante.'],
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
        $fabricante = $this->fabricanteRepository->findById($idAntiguo);

        if ($fabricante != null) {
            $fotoAntigua = $fabricante[0]->getFoto();

            $this->entityManager->remove($fabricante[0]);
            $this->entityManager->flush();

            $filesystem = new Filesystem();
            $rutaFotoAntigua = $this->getParameter('directorio_uploads_fabricantes') . '/' . $fabricante[0]->getFoto();

            if ($filesystem->exists($rutaFotoAntigua)) {
                $filesystem->remove($rutaFotoAntigua);
            }

            $nombre = $request->request->get('nombre');
            $descripcion = $request->request->get('descripcion');
            $foto = $request->files->get('foto');

            if ($nombre == null || $descripcion == null || $nombre == null && $descripcion == null) {
                $errors[] = 'No puedes crear un fabricante sin nombre y/o descripción.';
            }

            if (strlen($nombre) > 50) {
                $errors[] = 'No puedes superar los 50 caracteres.';
            }

            if (strlen($descripcion) > 255) {
                $errors[] = 'No puedes superar los 255 caracteres en la descripción.';
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
                $fabricante = new Fabricante();
                $fabricante->setUuid(Uuid::v7());
                $fabricante->setNombre($nombre);
                $fabricante->setDescripcion($descripcion);

                if ($foto != null) {
                    $fotoNuevoNombre = uniqid(true) . '.' . $foto->guessExtension();

                    try {
                        $foto->move(
                            $this->getParameter('directorio_uploads_fabricantes'),
                            $fotoNuevoNombre
                        );
                    } catch (FileException $ex) {
                        $this->addFlash('error', 'Error al guardar la foto del fabricante.');
        
                        return $this->redirectToRoute('admin_fabricantes');
                    }

                    $fabricante->setFoto($fotoNuevoNombre);
                } else {
                    $fabricante->setFoto($fotoAntigua);
                }

                try {
                    $this->entityManager->persist($fabricante);
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
                ['response' => 'error', 'error' => 'No se encontró el fabricante.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
