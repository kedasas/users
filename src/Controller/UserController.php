<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller used to manage users in the application.
 *
 * @author Kęstutis
 */

/**
 * @IsGranted("ROLE_USER")
 *
 * All authenticated users can access this controller
 */
class UserController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/users", name="users_list")
     * @IsGranted("ROLE_USER")
     *
     * All authenticated users can access this route.
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/user.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/users/new", name="new_user", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * Only user with ROLE_ADMIN can access this route.
     */
    public function newAction(Request $request)
    {
        $user = new User();
        //building form
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            //setting ROLE_USER when creating new user
            $user->setRoles(['ROLE_USER']);
            $password = $this->encoder->encodePassword($user, $form->get('Password')->getData());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User "'.$user->getName().'" is created!');

            return $this->redirectToRoute('users_list');
        }

        return $this->render('user/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/edit/{id}", name="edit_user", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * Only user with ROLE_ADMIN can access this route.
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)->find($id);
        //building form
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->encoder->encodePassword($user, $form->get('Password')->getData());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'User is updated!');

            return $this->redirectToRoute('edit_user', [
                'id' => $user->getId()
            ]);

        }

        return $this->render('user/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/delete/{id}", name="delete_user",)
     * @IsGranted("ROLE_ADMIN")
     *
     * Only user with ROLE_ADMIN can access this route.
     * This is ajax request and Json response.
     */

    // methods={"DELETE"}
    public function deleteAction($id)
    {
        $curUser = parent::getUser();

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if(!empty($user)){
            //prevent deleting yourself
            if ($curUser->getId() == $user->getId()) {
                $msg = "You can't delete yourself";
                return new JsonResponse(['error' => $msg]);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $msg = 'User "'.$user->getName().'" is deleted!';

            return new JsonResponse(['success' => $msg]);
        }else
            return new JsonResponse(['error' => "User doesn't exist"]);

    }

}
