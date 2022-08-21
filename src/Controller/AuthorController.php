<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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

    #[Route('/api/author', name: 'createAuthor', methods: ['POST'])]
    public function createAuthor(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        //Request Content Post and deserialize in author object
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json');
        $entityManager->persist($author);
        $entityManager->flush();

        //return author when is created
        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthor']);

        //get url of detailBook where id = $author->getId()
        $location = $urlGenerator->generate('detailBook', ['id' => $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        //return $jsonBook
        return new JsonResponse($jsonAuthor, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/author/{id}', name: 'updateAuthor', methods: ['PUT'])]
    public function updateAuthor(Request $request, SerializerInterface $serializer, Author $currentAuthor, EntityManagerInterface $entityManager): JsonResponse
    {
        //deserialize $currentBook to write in
        $updatedAuthor = $serializer->deserialize($request->getContent(), Author::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthor]);

        $entityManager->persist($updatedAuthor);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
