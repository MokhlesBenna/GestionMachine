<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Security\LoginFormAuthenticator;
class SecurityController extends AbstractController
{
    public const AUTHENTICATION_ERROR = 'authentication_error';
    #[Route(path: '/login', name: 'app_login')]
 
public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
{
    $user = $this->getUser();

    if ($user) {
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->redirectToRoute('admin_dashboard');
        }

        if (in_array('ROLE_TECHNICIEN', $roles)) {
            return $this->redirectToRoute('technicien_dashboard');
        }
    }

    // Check error from session (manual error message from authenticator)
    $session = $request->getSession();
    $customError = $session->get(LoginFormAuthenticator::AUTHENTICATION_ERROR);
    if ($customError) {
        $session->remove(LoginFormAuthenticator::AUTHENTICATION_ERROR);
    }

    return $this->render('security/login.html.twig', [
        'last_username' => $authenticationUtils->getLastUsername(),
        'error' => $customError ?: $authenticationUtils->getLastAuthenticationError(),
    ]);
}


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony handles logout automatically via firewall
        throw new \LogicException('Logout is handled by Symfony firewall.');
    }

    #[Route(path: '/profile', name: 'user_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/adminProfile.html.twig', [
            'user' => $user,
        ]);
    }
}
