<?php

namespace App\Twig;

use App\Repository\NotificationRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class NotificationExtension extends AbstractExtension implements GlobalsInterface
{
    private NotificationRepository $notificationRepository;
    private TokenStorageInterface $tokenStorage;

    public function __construct(NotificationRepository $notificationRepository, TokenStorageInterface $tokenStorage)
    {
        $this->notificationRepository = $notificationRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function getGlobals(): array
    {
        $user = null;
        $token = $this->tokenStorage->getToken();

        if ($token && is_object($token->getUser())) {
            $user = $token->getUser();
        }

        $notifications = [];

        if ($user && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $notifications = $this->notificationRepository->findUnreadAdminNotifications();
        }

        return [
            'notifications' => $notifications,
        ];
    }
}
