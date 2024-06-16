<?php

namespace App\Security\Authentication;

use \DateTime;
use App\Entity\Acceso;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Uid\Uuid;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    use TargetPathTrait;

    protected HttpUtils $httpUtils;
    protected EntityManagerInterface $entityManager;
    protected array $options = [];
    protected ?LoggerInterface $logger;
    protected ?string $firewallName = null;
    protected array $defaultOptions = [
        'always_use_default_target_path' => false,
        'default_target_path' => '/',
        'login_path' => '/login',
        'target_path_parameter' => '_target_path',
        'use_referer' => false,
    ];

    public function __construct(HttpUtils $httpUtils, EntityManagerInterface $entityManager, ?LoggerInterface $logger = null)
    {
        $this->httpUtils = $httpUtils;
        $this->logger = $logger;
        $options = [];
        $this->setOptions($options);
        $this->entityManager = $entityManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $ususario = $token->getUser();

        $acceso = new Acceso();
        $acceso->setUuid(Uuid::v7());
        $acceso->setFecha(new DateTime('now'));
        $acceso->setResultado('Success');
        $acceso->setUsuario($ususario);

        $this->entityManager->persist($acceso);
        $this->entityManager->flush();

        return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
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

    public function getFirewallName(): ?string
    {
        return $this->firewallName;
    }

    public function setFirewallName(string $firewallName): void
    {
        $this->firewallName = $firewallName;
    }

    /**
     * Builds the target URL according to the defined options.
     */
    protected function determineTargetUrl(Request $request): string
    {
        if ($this->options['always_use_default_target_path']) {
            return $this->options['default_target_path'];
        }

        $targetUrl = ParameterBagUtils::getRequestParameterValue($request, $this->options['target_path_parameter']);

        if (\is_string($targetUrl) && (str_starts_with($targetUrl, '/') || str_starts_with($targetUrl, 'http'))) {
            return $targetUrl;
        }

        if ($this->logger && $targetUrl) {
            $this->logger->debug(sprintf('Ignoring query parameter "%s": not a valid URL.', $this->options['target_path_parameter']));
        }

        $firewallName = $this->getFirewallName();
        if (null !== $firewallName && !$request->attributes->getBoolean('_stateless') && $targetUrl = $this->getTargetPath($request->getSession(), $firewallName)) {
            $this->removeTargetPath($request->getSession(), $firewallName);

            return $targetUrl;
        }

        if ($this->options['use_referer'] && $targetUrl = $request->headers->get('Referer')) {
            if (false !== $pos = strpos($targetUrl, '?')) {
                $targetUrl = substr($targetUrl, 0, $pos);
            }
            if ($targetUrl && $targetUrl !== $this->httpUtils->generateUri($request, $this->options['login_path'])) {
                return $targetUrl;
            }
        }

        return $this->options['default_target_path'];
    }
}
