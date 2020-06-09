<?php

namespace App\Controller\Security;

use App\Entity\Person\Person;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\Bundle\OAuth2Bundle\Manager\AccessTokenManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Manager\RefreshTokenManagerInterface;

/**
 * OAuth2 API controller.
 *
 * @Route("/", name="oauth2_")
 */
class OAuth2Controller extends AbstractFOSRestController
{
    /**
     * Lists all persons.
     *
     * @Route("/revoke", name="revoke", methods={"POST"})
     */
    public function revokeAction(Request $request, RefreshTokenManagerInterface $refresh, AccessTokenManagerInterface $access)
    {
        $em = $this->getDoctrine()->getManager();
        $rawToken = $request->request->getAlnum('token', null);
        
        if ($refreshToken = $refresh->find($rawToken)) {
            $refresh->save($refreshToken->revoke());

            return $this->view(null, 200);
        }

        if ($accessToken = $access->find($rawToken)) {
            $access->save($accessToken->revoke());

            return $this->view(null, 200);
        }

        return $this->view([
            "error" => "invalid_token",
            "error_description" => "Token not found.",
            "message" => "Token revocation failed",
        ], 404);
    }
}