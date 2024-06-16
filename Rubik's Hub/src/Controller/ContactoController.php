<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/contacto', requirements: ['_locale' => 'es'], name: 'contacto_')]
class ContactoController extends AbstractController
{
    #[Route('/enviar', name: 'enviar')]
    public function enviar(MailerInterface $mailer, Request $request): JsonResponse
    {
        $destinatario = $request->request->get('destinatario');
        $asunto = $request->request->get('asunto');
        $mensaje = $request->request->get('mensaje');

        if ($destinatario == null || $asunto == null || $mensaje == null) {
            $errors[] = 'Te faltan campos por rellenar.';
        }

        if (empty($errors)) {
            $email = (new Email())
            ->from('alberto@rubikshub.es')
            ->to($destinatario)
            ->subject($asunto)
            ->text($mensaje);
    
            try {
                $mailer->send($email);
                return $this->json(
                    ['response' => 'ok'],
                    JsonResponse::HTTP_OK
                );
            } catch (\Exception $ex) {
                return $this->json(
                    ['response' => 'error', 'error' => 'Error al enviar el correo electrónico: ' . $ex->getMessage()],
                    JsonResponse::HTTP_INTERNAL_SERVER_ERROR
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
