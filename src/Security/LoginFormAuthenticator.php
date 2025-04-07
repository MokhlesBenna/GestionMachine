<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

use App\Controller\SecurityController;
use App\Repository\AdminRepository;
use App\Repository\TechnicienRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Psr\Log\LoggerInterface;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
        public const AUTHENTICATION_ERROR = 'authentication_error';

    private RouterInterface $router;
    private AdminRepository $adminRepository;
    private TechnicienRepository $technicienRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private LoggerInterface $logger;

    public function __construct(
        RouterInterface $router,
        AdminRepository $adminRepository,
        TechnicienRepository $technicienRepository,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger
    ) {
        $this->router = $router;
        $this->adminRepository = $adminRepository;
        $this->technicienRepository = $technicienRepository;
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = trim($request->request->get('email', ''));
        $password = $request->request->get('password', '');
        $userType = $request->request->get('user_type', 'technicien');
    
        if (empty($email) || empty($password)) {
            throw new CustomUserMessageAuthenticationException('Veuillez remplir tous les champs.');
        }
    
        return new Passport(
            new UserBadge($email, function ($userIdentifier) use ($userType) {
                if ($userType === 'admin') {
                    return $this->adminRepository->findOneBy(['email' => $userIdentifier]);
                } else {
                    return $this->technicienRepository->findOneBy(['email' => $userIdentifier]);
                }
            }),
            new CustomCredentials(
                function ($credentials, $user): bool {
                    return $user && $user->getPassword() === $credentials;
                },
                $password
            )
        );
    }
    

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
    
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        }
    
        if (in_array('ROLE_TECHNICIEN', $user->getRoles(), true)) {
            return new RedirectResponse($this->router->generate('technicien_dashboard'));
        }
    
        // Optional fallback
        throw new \LogicException('No matching role found for user.');
    }
    

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $this->logger->error('Authentication failed: ' . $exception->getMessage());
    
        $request->getSession()->set(SecurityController::AUTHENTICATION_ERROR, new CustomUserMessageAuthenticationException($exception->getMessage()));
    
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }
}