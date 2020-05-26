<?php

namespace App\Controller\Api;

use App\Entity\Person\Person;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * Person API controller.
 *
 * @Route("/api/person", name="api_person_")
 */
class PersonController extends AbstractFOSRestController
{
    /**
     * Lists all persons.
     *
     * @Route("/", name="list", methods={"GET"}, defaults={
     *      "oauth2_scopes": {"admin"}
     * })
     * @View(serializerGroups={"list"})
     */
    public function listPersonAction()
    {
        $em = $this->getDoctrine()->getManager();
        $persons = $em->getRepository(Person::class)->findAll();

        return $this->view($persons);
    }
}