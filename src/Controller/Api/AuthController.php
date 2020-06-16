<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * User API controller.
 *
 * @Route("/api/user", name="api_security_")
 */
class AuthController extends AbstractFOSRestController
{
    /**
     * Shows current authenticated user.
     *
     * @Route("/", name="show", methods={"GET"})
     * @View(serializerGroups={"details", "list"})
     */
    public function showAction()
    {
        return $this->view($this->getUser());
    }
}