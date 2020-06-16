<?php

namespace App\Controller\Admin\OAuth;

use App\Template\Annotation\MenuItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * Creates a new client.
     *
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('App\Form\OAuth2\ClientNewType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $client = new Client($data['identifier'], $data['secret']);

            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('admin_client_edit', ['id' => $client->getIdentifier()]);
        }

        return $this->render('admin/client/new.html.twig', [
            'form' => $form->createView(),
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

    /**
     * Displays a form to edit an existing client.
     *
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Client $client)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('App\Form\OAuth2\ClientType', $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_client_show', ['id' => $client->getIdentifier()]);
        }

        return $this->render('admin/client/edit.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a person entity.
     *
     * @Route("/{id}/delete", name="delete")
     */
    public function deleteAction(Request $request, Client $client)
    {
        $form = $this->createDeleteForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($client);
            $em->flush();

            return $this->redirectToRoute('admin_client_index');
        }

        return $this->render('admin/client/delete.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a form to check out all checked in users.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Client $client)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_client_delete', ['id' => $client->getIdentifier()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
