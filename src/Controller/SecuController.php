<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\User\UserInterface;


class SecuController extends AbstractController
{
    #[Route('/account/new', name: 'secu_newAccount')]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder ): Response
    {
        
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $user->setAdmin(False);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('secu_login');
        }
        return $this->render('secu/registration.html.twig', [
            'formRegis' => $form->createView(),
            'editMode' => $user->getId() !==null
        ]);

       
        
    }
    #[Route('/login', name: 'secu_login')]
    public function login(){
        return $this->render('secu/login.html.twig');

    }

    #[Route('/logout', name: 'secu_logout')]
    public function logout() {}

    #[Route('/account', name: 'secu_account')]
    public function account(PostRepository $repo, UserInterface $user){
        $posts = $repo->findBy(array('createdBy' => $user->getUsername()));

        return $this->render('secu/account.html.twig', [
            'controller_name' => 'SecuController',
            'posts' => $posts 
        ]);

    }

    #[Route('/admin', name: 'secu_admin')]
    public function admin(PostRepository $repo){
        $posts = $repo->findAll();
        return $this->render('secu/admin.html.twig', [
            'controller_name' => 'SecuController',
            'posts' => $posts,
        ]);

    }
}
