<?php

namespace App\DependencyInjection;

use App\Repository\AuthRepository;
use League\OAuth2\Server\AuthorizationServer;
use OpenIDConnectServer\ClaimExtractor;
use OpenIDConnectServer\IdTokenResponse;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class OpenIdPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $authRepository = $container->getDefinition(AuthRepository::class);
        $claimExtractor = new Definition(ClaimExtractor::class);

        $idTokenResponse = new Definition(IdTokenResponse::class, [
            $authRepository,
            $claimExtractor,
        ]);

        $authorizationServer = $container->getDefinition(AuthorizationServer::class);
        $authorizationServer->setArgument('$responseType', $idTokenResponse);
    }
}