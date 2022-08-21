<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    #[Route('/api/author', name: 'author', methods: ['GET'])]
    public function getAllAuthor(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        //Find all author in Repository
        $authorList = $authorRepository->findAll();

        //Convert $authorList (Objet PHP) in json
        $jsonBookList = $serializer->serialize($authorList, 'json', ['groups' => 'getAuthor']);

        //Return $jsonBookList already in json
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/author/{id}', name: 'detailAuthor', methods: ['GET'])]
    public function getDetailAuthor(Author $author, SerializerInterface $serializer): JsonResponse
    {
        // Find author by id and serialize in json
        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthor']);

        //Return $jsonAuthor already in json
        return new JsonResponse($jsonAuthor, Response::HTTP_OK, [], true);
    }
}
