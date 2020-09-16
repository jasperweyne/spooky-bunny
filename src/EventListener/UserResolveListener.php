<?php

namespace App\EventListener;

use App\Security\AuthUserProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;

final class UserResolveListener
{
    /**
     * @var AuthUserProvider
     */
    private $userProvider;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @param AuthUserProvider $userProvider
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(AuthUserProvider $userProvider, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userProvider = $userProvider;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param UserResolveEvent $event
     */
    public function onUserResolve(UserResolveEvent $event): void
    {
        $user = $this->userProvider->loadUserByEmail($event->getUsername());

        if (null === $user) {
            return;
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $event->getPassword())) {
            return;
        }

        $event->setUser($user);
    }
}