<?php

namespace App\Controller\Admin\OAuth;

use App\Template\Annotation\MenuItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;

/**
 * Mail controller.
 *
 * @Route("/admin/client", name="admin_client_")
 */
class ClientController extends AbstractController
{
    /**
     * Lists all mails.
     *
     * @MenuItem(title="Clients", menu="admin")
     * @Route("/", name="index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clients = $em->getRepository(Client::class)->findAll();

        return $this->render('admin/client/index.html.twig', [
            'clients' => $clients,
        ]);
    }

    /**
     * Finds and displays a mail entity.
     *
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function showAction(Client $client)
    {
        return $this->render('admin/client/show.html.twig', [
            'client' => $client
        ]);
    }
}
