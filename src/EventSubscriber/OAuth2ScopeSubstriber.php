<?php

namespace App\EventSubscriber;

use App\Repository\AuthRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\ScopeResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

class OAuth2ScopeSubstriber implements EventSubscriberInterface
{
    private $identityProvider;

    public function __construct(AuthRepository $identityProvider)
    {
        $this->identityProvider = $identityProvider;
    }
    
    public function onScopeResolve(ScopeResolveEvent $event): void
    {
        $requestedScopes = $event->getScopes();

        $user = $this->identityProvider->getUserEntityByIdentifier($event->getUserIdentifier());
        
        if (is_null($user) || !$user instanceof UserInterface)
            return;

        $admin = array_search("admin", $requestedScopes);
        if (false !== $admin && !in_array("ROLE_ADMIN", $user->getRoles())) {
            unset($requestedScopes[$admin]);
        }

        $event->setScopes(...array_values($requestedScopes));
    }

    public static function getSubscribedEvents()
    {
        return [
            OAuth2Events::SCOPE_RESOLVE => ['onScopeResolve'],
        ];
    }
}
