<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Article;
use App\Dto\CreateArticleDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Throwable;

final class ArticleController extends AbstractController
{
    //API che restituisce la lista di articoli
    #[Route('/api/articles', name: 'app_article', methods: ['GET'])]
    #[OA\Get(
        path: '/api/articles',
        summary: 'Returns the list of all the articles in the catalog.',
        tags: [
            'Articles management'
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'articles_list',
                    description: 'Returns the list of all the articles in the catalog',
                    summary: 'Returns the list of all the articles in the catalog.',
                    value: '{
                             "articles": [
                                {
                                    "id": 1,
                                    "codeId": "efdgdvsg",
                                    "name": "prova",
                                    "price": 2
                                },
                                {
                                    "id": 2,
                                    "codeId": "sf4rf",
                                    "name": "test",
                                    "price": 13.5
                                },
                            ]
                        }'
                )
            ]
        )
    )]
    #[
        OA\Response(
            response: 500,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'server_error',
                        summary: 'Some error occurred during the operations',
                        description: 'Some error occurred during the operations',
                        value: '{
                        "error": "Error on getting articles list"
                    }'
                    )
                ]
            )
        )
    ]
    public function index(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        try {
            $repository = $entityManager->getRepository(Article::class);
            $articles = $repository->findAll();

            $formattedArticles = [];
            foreach ($articles as $article) {
                array_push($formattedArticles, json_decode($serializer->serialize($article, 'json'), true));
            }

            $json = new JsonResponse();
            $json->setData(['articles' =>  $formattedArticles]);
            $json->setStatusCode(200, "OK");
            return $json;
        } catch (Throwable $e) {
            $json = new JsonResponse();
            $json->setData(['error' => "Error on getting articles list"]);
            $json->setStatusCode(500, "KO");
            return $json;
        }
    }

    #[Route('/api/articles/{code_id}', name: 'get_article', methods: ['GET'])]
    #[OA\Get(
        path: '/api/articles/{code_id}',
        summary: 'Returns the article with the specified codeId',
        tags: [
            'Articles management'
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'article_retrieve',
                    description: 'Returns the article with the specified code_id.',
                    summary: 'Returns the article with the specified code_id.',
                    value: '{
                             "article": 
                                {
                                    "id": 1,
                                    "codeId": "efdgdvsg",
                                    "name": "prova",
                                    "price": 2
                                }
                        }'
                )
            ]
        )
    )]
    #[
        OA\Response(
            response: 500,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'server_error',
                        summary: 'Some error occurred during the operations',
                        description: 'Some error occurred during the operations',
                        value: '{
                        "error": "Error on getting article"
                    }'
                    )
                ]
            )
        )
    ]
    #[OA\Parameter(
        name: 'code_id',
        in: 'path',
        description: 'This field represent the identification code of the article. It must be unique',
        schema: new OA\Schema(type: 'string', description: 'This field represent the identification code of the article. It must be unique', example: 'sdgvsg4522')

    )]
    public function get(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer) {
        try {
            $codeId = $request->get('code_id');
            $repository = $entityManager->getRepository(Article::class);
            $article = $repository->findOneBy(['codeId' => $codeId]);
            $json = new JsonResponse();
            $json->setData(['article' => json_decode($serializer->serialize($article, 'json'))]);
            $json->setStatusCode(200, "OK");
            return $json;
        }
        catch(Throwable $e) {
            $json = new JsonResponse();
            $json->setData(['error' => "Error on getting article"]);
            $json->setStatusCode(500, "KO");
            return $json;
        }
    }
    //API che restituisce la lista di articoli
    #[Route('/api/articles', name: 'article_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/articles',
        summary: 'The id of the created article.',
        tags: [
            'Articles management'
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'article_creation',
                    description: 'The result in case of creation successfully made.',
                    summary: 'The result in case of creation successfully made.',
                    value: '{
                            "result": "Article created",
                            "id": "150",
                        }'
                )
            ]
        )
    )]
    #[
        OA\Response(
            response: 422,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'invalid_request',
                        summary: 'The result object in case of invalid request',
                        description: 'The result object in case of invalid request',
                        value: '{
                        "error": "Invalid request format"
                    }'
                    )
                ]
            )
        )
    ]
    #[
        OA\Response(
            response: 500,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'server_error',
                        summary: 'Some error occurred during the operations',
                        description: 'Some error occurred during the operations',
                        value: '{
                        "error": "Error on article creation"
                    }'
                    )
                ]
            )
        )
    ]
    #[OA\Parameter(
        name: 'code_id',
        in: 'query',
        description: 'This field represent the identification code of the article. It must be unique',
        schema: new OA\Schema(type: 'string', description: 'This field represent the identification code of the article. It must be unique', example: 'sdgvsg4522')

    )]
    #[OA\Parameter(
        name: 'name',
        in: 'query',
        description: 'This field represent the name of the article',
        schema: new OA\Schema(type: 'string', description: 'This field represent the name of the article', example: 'Slim fit jeans')

    )]
    #[OA\Parameter(
        name: 'price',
        in: 'query',
        description: 'This field represent the name of the article',
        schema: new OA\Schema(type: 'float', description: 'This field represent the price of the article', example: '15,4')

    )]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        try {
            $createPost = new CreateArticleDto(
                $request->get('code_id'), 
                $request->get('name'), $request->get('price'));
            $errors = $validator->validate($createPost);

            if (count($errors) > 0) {
                /*
                 * Uses a __toString method on the $errors variable which is a
                 * ConstraintViolationList object. This gives us a nice string
                 * for debugging.
                 */
                $errorsString = (string) $errors;
            
                return $this->json([
                            'status'  => 'error',
                            'message' => $errorsString
                        ], 422);
            }

            $article = new Article();
            $article->setCodeId($createPost->code_id);
            $article->setName($createPost->name);
            $article->setPrice($createPost->price);

            
            $entityManager->persist($article);
            $entityManager->flush();

            $json = new JsonResponse();
            $json->setData(['result' => "Article created", "id" => $article->getId()]);
            $json->setStatusCode(200, "OK");
            return $json;
        } catch (Throwable $e) {
            $json = new JsonResponse();
            $json->setData(['error' => "Error on article creation"]);
            $json->setStatusCode(500, "KO");
            return $json;
        }
    }
}
