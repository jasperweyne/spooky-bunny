<?php

namespace App\DependencyInjection;

use App\Repository\AuthRepository;
use App\Security\ClaimExtractor;
use App\Security\OpenIdGrant;
use App\Security\OpenIdResponse;
use DateInterval;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class OpenIdPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //Replace the AuthCodeGrant with the openId grant.
        $openIdAuthGrant = new Definition(OpenIdGrant::class);
        $openIdAuthGrant->addArgument(new Reference('League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface'));
        $openIdAuthGrant->addArgument(new Reference('League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface'));
        $openIdAuthGrant->addArgument(new Reference('App\Repository\NonceRepository'));
        $dateInterval = new Definition(DateInterval::class, ['PT1M']);
        $openIdAuthGrant->addArgument($dateInterval);

        $container->setDefinition(AuthCodeGrant::class, $openIdAuthGrant);

        //Configure the openId response.
        $authRepository = $container->getDefinition(AuthRepository::class);
        $claimExtractor = new Definition(ClaimExtractor::class);

        $idTokenResponse = new Definition(OpenIdResponse::class, [
            $authRepository,
            $claimExtractor,
            new Reference('App\Repository\NonceRepository'),
        ]);

        $authorizationServer = $container->getDefinition(AuthorizationServer::class);
        $authorizationServer->setArgument('$responseType', $idTokenResponse);
    }
}
