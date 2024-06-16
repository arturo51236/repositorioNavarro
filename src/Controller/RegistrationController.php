<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use App\Repository\UsuarioRepository;
use App\Security\EmailVerifier;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/registro', name: 'registro')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuario();

        $jsonPaisesRuta = $this->getParameter('directorio_static_ubicacion') . '/ISO-3166-1.json';
        $jsonPaisesData = json_decode(file_get_contents($jsonPaisesRuta), true);

        // Temporal (Solo ES): Incluir todos los equivalentes a provincias de los 244 países
        $jsonESRuta = $this->getParameter('directorio_static_ubicacion') . '/ES.json';
        $jsonESData = json_decode(file_get_contents($jsonESRuta), true);

        $paises = [];
        if ($jsonPaisesData !== null) {
            foreach ($jsonPaisesData as $pais) {
                $paises[$pais['Country']] = $pais['Alpha-2 Code'];
            }
        }

        $es = [];
        if ($jsonESData !== null) {
            foreach ($jsonESData as $provincia) {
                $es[$provincia['label']] = $provincia['code'];
            }
        }

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'paises' => $paises,
            'es' => $es,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUuid(Uuid::v7());
            $user->setNombre($form->get('nombre')->getData());
            $user->setApellidos($form->get('apellidos')->getData());
            $user->setDni($form->get('dni')->getData());
            // encode the plain password
            $user->setPassword(
                    $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $foto = $form->get('foto')->getData();

            if ($foto != null) {
                $fotoNuevoNombre = uniqid(true) . '.' . $foto->guessExtension();

                try {
                    $foto->move(
                        $this->getParameter('directorio_uploads_usuarios'),
                        $fotoNuevoNombre
                    );
                } catch (FileException $exception) {
                    $this->addFlash('error', 'Error al guardar la foto de perfil');
    
                    return $this->redirectToRoute('registro');
                }
    
                $user->setFoto($fotoNuevoNombre);
            }

            $user->setPais($form->get('pais')->getData());
            $user->setProvincia($form->get('provincia')->getData());
            $user->setCp($form->get('cp')->getData());
            $user->setDireccion($form->get('direccion')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('verificar_usuario', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@rubikshub.es', 'Respuestas automáticas - Rubik\'s Hub'))
                    ->to($user->getEmail())
                    ->subject('Confirmación de correo electrónico')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'verificar_usuario')]
    public function verifyUserEmail(Request $request, UsuarioRepository $usuarioRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('registro');
        }

        $user = $usuarioRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('registro');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', 'Error al verificar tu cuenta de usuario');

            return $this->redirectToRoute('registro');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Tu cuenta de usuario se ha verificado correctamente.');

        return $this->redirectToRoute('/');
    }
}
