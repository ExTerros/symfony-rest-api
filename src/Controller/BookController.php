<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'book', methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        //Find all books in Repository
        $bookList = $bookRepository->findAll();

        //Convert $bookList (Objet PHP) in json
        $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);

        //Return $jsonBookList already in json
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }


//      Without sensio/framework-extra-bundle
//    #[Route('/api/books/{id}', name: 'detailBook', methods: ['GET'])]
//    public function getDetailBook(int $id, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
//    {
//        //Find book by id
//        $book = $bookRepository->findBy(
//            ['id' => $id]
//        );
//
//        //if the book exists
//        if ($book) {
//            //Convert $book (Objet PHP) in json
//            $jsonBookList = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
//
//            //Return $jsonBookList already in json
//            return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
//        }
//
//        //if the book does not exist return 404
//        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
//    }

    //With sensio/framework-extra-bundle
    #[Route('/api/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function getDetailBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
        // Find book by id and serialize in json
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);

        //Return $jsonBook already in json
        return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
    }

    #[Route('/api/books/{id}', name: 'deleteBook', methods: ['DELETE'])]
    public function deleteBook(Book $book, EntityManagerInterface $entityManager): JsonResponse
    {
        //remove $book by id
        $entityManager->remove($book);
        $entityManager->flush();

        //return null 204
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books', name: 'createBook', methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        //Request Content Post and deserialize in book object
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');

        $entityManager->persist($book);
        $entityManager->flush();

        //return book when is created
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);

        //get url of detailBook where id = $book->getId()
        $location = $urlGenerator->generate('detailBook', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        //return $jsonBook
        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
