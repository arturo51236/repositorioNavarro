<?php

namespace App\Security\Authentication;

use \DateTime;
use App\Entity\Acceso;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Uid\Uuid;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    protected HttpKernelInterface $httpKernel;
    protected HttpUtils $httpUtils;
    protected EntityManagerInterface $entityManager;
    protected array $options = [];
    protected ?LoggerInterface $logger;
    protected array $defaultOptions = [
        'failure_path' => null,
        'failure_forward' => false,
        'login_path' => '/login',
        'failure_path_parameter' => '_failure_path',
    ];

    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, EntityManagerInterface $entityManager, ?LoggerInterface $logger = null)
    {
        $this->httpKernel = $httpKernel;
        $this->httpUtils = $httpUtils;
        $this->logger = $logger;
        $options = [];
        $this->setOptions($options);
        $this->entityManager = $entityManager;
    }

    /**
     * Gets the options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->defaultOptions, $options);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $acceso = new Acceso();
        $acceso->setUuid(Uuid::v7());
        $acceso->setFecha(new DateTime('now'));
        $acceso->setResultado('Failure');
        $acceso->setUsuario(null);

        $this->entityManager->persist($acceso);
        $this->entityManager->flush();

        $options = $this->options;
        $failureUrl = ParameterBagUtils::getRequestParameterValue($request, $options['failure_path_parameter']);

        if (\is_string($failureUrl) && (str_starts_with($failureUrl, '/') || str_starts_with($failureUrl, 'http'))) {
            $options['failure_path'] = $failureUrl;
        } elseif ($this->logger && $failureUrl) {
            $this->logger->debug(sprintf('Ignoring query parameter "%s": not a valid URL.', $options['failure_path_parameter']));
        }

        $options['failure_path'] ??= $options['login_path'];

        if ($options['failure_forward']) {
            $this->logger?->debug('Authentication failure, forward triggered.', ['failure_path' => $options['failure_path']]);

            $subRequest = $this->httpUtils->createRequest($request, $options['failure_path']);
            $subRequest->attributes->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);

            return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        $this->logger?->debug('Authentication failure, redirect triggered.', ['failure_path' => $options['failure_path']]);

        if (!$request->attributes->getBoolean('_stateless')) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        return $this->httpUtils->createRedirectResponse($request, $options['failure_path']);
    }
}
