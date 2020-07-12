<?php

namespace App\Controller\Admin;

use App\Entity\Security\Auth;
use App\Template\Annotation\MenuItem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Log\EventService;
use App\Log\Doctrine\EntityNewEvent;
use App\Log\Doctrine\EntityUpdateEvent;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Security controller.
 *
 * @Route("/admin/security", name="admin_security_")
 */
class SecurityController extends AbstractController
{
    private $events;

    public function __construct(EventService $events)
    {
        $this->events = $events;
    }

    /**
     * Lists all Contact entities.
     *
     * @MenuItem(title="Accounts", menu="admin", activeCriteria="admin_security_")
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $accounts = $em->getRepository(Auth::class)->findAll();

        return $this->render('admin/security/index.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * Finds and displays an auth entity.
     *
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function showAction(Auth $auth)
    {
        $em = $this->getDoctrine()->getManager();

        $createdAt = $this->events->findOneBy($auth, EntityNewEvent::class);
        $modifs = $this->events->findBy($auth, EntityUpdateEvent::class);

        return $this->render('admin/security/show.html.twig', [
            'createdAt' => $createdAt,
            'modifs' => $modifs,
            'auth' => $auth,
        ]);
    }
    
    /**
     * Displays a form to edit roles.
     *
     * @Route("/{id}/roles", name="roles", methods={"GET", "POST"})
     */
    public function rolesAction(Request $request, Auth $auth)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createRoleForm($auth);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $roles = array();
            if ($data['admin']) {
                $roles[] = "ROLE_ADMIN";
            }

            $auth->setRoles($roles);

            $em->persist($auth);
            $em->flush();
            
            $this->addFlash('success', 'Rollen bewerkt');
            return $this->redirectToRoute('admin_security_show', ['id' => $auth->getPerson()->getId()]);
        }

        return $this->render('admin/security/roles.html.twig', [
            'form' => $form->createView(),
            'auth' => $auth,
        ]);
    }
    
    private function createRoleForm(Auth $auth)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_security_roles', ['id' => $auth->getPerson()->getId()]))
            ->add('admin', CheckboxType::class, [
                'required' => false,
                'attr' => in_array("ROLE_ADMIN", $auth->getRoles()) ? ['checked' => 'checked'] : [],
            ])
            ->getForm()
        ;
    }
}
