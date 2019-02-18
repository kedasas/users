<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


/**
 * Controller used to manage groups in the application.
 *
 * @author Kęstutis
 */

/**
 * @IsGranted("ROLE_USER")
 *
 * All authenticated users can access this controller
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/groups", name="group_list")
     */
    public function index()
    {
        $groups = $this->getDoctrine()->getRepository(Group::class)->findAll();

        return $this->render('group/group.html.twig', [
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/groups/new", name="new_group")
     * @IsGranted("ROLE_ADMIN")
     *
     * Only user with ROLE_ADMIN can access this route.
     */
    public function newAction(Request $request)
    {
        $group = new Group();

        $form = $this->createForm(GroupType::class, $group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            $this->addFlash('success', 'Group "'.$group->getName().'" is created!');

            return $this->redirectToRoute('group_list');

        }

        return $this->render('group/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/groups/edit/{id}", name="edit_group",methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * Only user with ROLE_ADMIN can access this route.
     */
    public function editAction(Request $request, $id)
    {
        $group = $this->getDoctrine()
            ->getRepository(Group::class)->find($id);

        $form = $this->createForm(GroupType::class, $group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Group is updated!');

            return $this->redirectToRoute('edit_group', [
                'id' => $group->getId()
            ]);
        }


        return $this->render('group/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/groups/delete/{id}", name="delete_group", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     *
     * Only user with ROLE_ADMIN can access this route.
     * This is ajax request and Json response.
     */
    public function deleteAction($id)
    {
        $group = $this->getDoctrine()->getRepository(Group::class)->find($id);

        if(!empty($group)){
            //if group is not empty return error
            if (!$group->getUsers()->isEmpty()) {
                $msg = "You can not delete this group, there are some users in it, at first you need to remove users from this group !";
                return new JsonResponse(['error' => $msg]);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush();

            $msg = 'Group "'.$group->getName().'" is deleted!';

            return new JsonResponse(['success' => $msg]);

        }else
            return new JsonResponse(['error' => "Group doesn't exist"]);

    }

}
