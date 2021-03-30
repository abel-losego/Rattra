<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Form\PostType;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;


class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function index(PostRepository $repo): Response
    {
        $posts = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'posts' => $posts,
        ]);
    }

    #[Route('/', name:'home')]
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue ici",
        ]);
    }
    
    #[Route('/blog/new', name:"blog_new")]
    #[Route('/blog/{id}/edit', name:"blog_edit")]
    public function form(Post $post = null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$post){
            $post = new Post();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$post->getId()){
                $post->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('blog_show',['id' => $post->getId()]);
        }
        return $this->render('blog/create.html.twig', [
            'formPost' => $form->createView(),
            'editMode' => $post->getId() !==null
        ]);
    }

    #[Route('/blog/{id}', name:"blog_show")]
    public function show(Post $post): Response
    {   
        return $this->render('blog/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/blog/{id}', name:"blog_show")]
    public function comment(Post $post): Response
    {   
        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
                ->add('createdAt')
                ->add('post', IntegerType::class)
                ->add('author', TextType::class, [
                    'attr' => [
                        'placeholder' => "Inserer le nom de l'auteur"
                    ]
                ])
                ->add('content', TextareaType::class, [
                    'attr' => [
                        'placeholder' => "Ecrivez votre commentaire "
                    ]
                ])
                ->getForm();
        
        
        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'formComment' => $form->createView()
        ]);
    }
}
