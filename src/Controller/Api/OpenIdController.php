<?php

namespace App\Controller\Api;

use App\Security\ClaimExtractor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Person API controller.
 *
 * @Route("/api/userinfo", name="api_userinfo_")
 */
class OpenIdController extends AbstractFOSRestController
{
    /**
     * Lists all persons.
     *
     * @Route("/", name="info", methods={"GET"})
     */
    public function infoAction(TokenStorageInterface $tokenStorage, ClaimExtractor $claimExtractor)
    {
        $scopes = $tokenStorage->getToken()->getAttribute('server_request')->getAttribute('oauth_scopes');
        $claims = $claimExtractor->extract($scopes, $this->getUser()->getClaims());
        $claims['sub'] = $this->getUser()->getIdentifier();
        return $this->view($claims);
    }
}