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

class SecuController extends AbstractController
{
    #[Route('/registration', name: 'secu_registration')]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder ): Response
    {
        
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $user->setAdmin(True);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('secu_login');
        }
        return $this->render('secu/registration.html.twig', [
            'formRegis' => $form->createView()
        ]);

       
        
    }
    #[Route('/login', name: 'secu_login')]
    public function login(){
        return $this->render('secu/login.html.twig');

    }

    #[Route('/logout', name: 'secu_logout')]
    public function logout() {}
}
