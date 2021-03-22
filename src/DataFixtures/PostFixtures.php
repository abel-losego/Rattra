<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //creation de 8 posts pour le dev
        for($i = 1; $i <=10; $i++){
            $post = new Post();
            $post->setTitle("Titre de l'article $i");
            $post->setContent("<p>Contenu de l'article $i</p>");
            $post->setCreatedBy("Auteur de l'article $i");
            $post->setImage("http://placehold.it/350x150");
            $post->setCreatedAt(new \DateTime());
            $manager->persist($post);

        }
        $manager->flush();
    }
}
