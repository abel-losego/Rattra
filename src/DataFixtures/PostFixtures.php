<?php
//Création de fakes données
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Comment;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
 
        for($i = 1; $i <=3; $i++){
            $category = new Category();
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());
            $manager->persist($category);

            for($j = 1; $j <=10; $j++){
                $post = new Post();
                $post->setTitle($faker->sentence())
                    ->setContent($faker->paragraph())
                    ->setCreatedBy($faker->name)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($post);

                for($k = 1; $k<=7; $k++){
                    $comment= new Comment();

                    $days = (new \DateTime())->diff($post->getCreatedAt())->days;
                    

                    $comment->setAuthor($faker->name)
                            ->setContent($faker->paragraph())
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                            ->setPost($post);
                    $manager->persist($comment);

                }
            }
        }
        

        
        $manager->flush();
    }}