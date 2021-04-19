<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Form\PostType;
use App\Form\CommentType;
use App\Form\RegistrationType;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    #[Route('/', name: 'index')]
    public function index(PostRepository $repo): Response
    {
        $posts = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'posts' => $posts,
        ]);
    }

    #[Route('/home', name:'home')]
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
    public function show(Post $post, Request $request, EntityManagerInterface $manager): Response
    {   

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            if(!$comment->getId()){
                $comment->setCreatedAt(new \DateTime('now'));
                $comment->setPost($post);
            }
            
            $manager->persist($comment);
            $manager->flush();
        }
        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'formComment' =>$form->createView()
        ]);
    }
    #[Route('/blog/delete/{id}', name:"blog_delete")]
    public function deletePost(Post $post, EntityManagerInterface $manager): Response 
    {     
        foreach ($post->getComments() as $comment) {
          $manager->remove($comment);
        }
     
        $manager->remove($post);
        $manager->flush();     
        return $this->redirectToRoute('blog');
      }
    
    #[Route('/blog/{id}/delete', name:"comment_delete")]
    public function deleteComment(Comment $comment, EntityManagerInterface $manager): Response 
    {     
        
        $manager->remove($comment);
        $manager->flush();     
        return $this->redirectToRoute('blog_show',['id' => $comment->getPost()]);
      }
}
