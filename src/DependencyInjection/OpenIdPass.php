<?php

namespace App\DependencyInjection;

use App\Repository\AuthRepository;
use App\Security\ClaimExtractor;
use App\Security\OpenIdResponse;
use League\OAuth2\Server\AuthorizationServer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class OpenIdPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $authRepository = $container->getDefinition(AuthRepository::class);
        $claimExtractor = new Definition(ClaimExtractor::class);

        $idTokenResponse = new Definition(OpenIdResponse::class, [
            $authRepository,
            $claimExtractor,
        ]);

        $authorizationServer = $container->getDefinition(AuthorizationServer::class);
        $authorizationServer->setArgument('$responseType', $idTokenResponse);
    }
}