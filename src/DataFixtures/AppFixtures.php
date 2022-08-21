<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        //add new Authors
        $listAuthor = [];
        for ($i=0; $i < 10; $i++)
        {
            //add Author
            $authors = new Author();
            $authors->setFirstName("Prénom " . $i)
            ->setLastName("Nom " . $i);
            $manager->persist($authors);
            //Save Author in array
            $listAuthor[] = $authors;

        }

        for ($i=0; $i < 20; $i++)
        {
            //add Book
            $book = new Book();
            $book->setTitle("Titre " . $i)
                ->setCoverText("Quatrième de couverture numéro : " . $i)
                //set Random Author by $listAuthor array
                ->setAuthor($listAuthor[array_rand($listAuthor)]);
            $manager->persist($book);
        }

        $manager->flush();
    }
}
