<?php

namespace App\Controller;

use App\Service\PriceService;
use App\Validator\PriceValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;
use OpenApi\Attributes as OA;

final class PriceController extends AbstractController
{
    // #[Route('/api/price', name: 'app_price')]
    // public function index(): Response
    // {
    //     return $this->render('price/index.html.twig', [
    //         'controller_name' => 'PriceController',
    //     ]);
    // }

    #[Route('/api/price/sum', name: 'price_sum', methods: ['POST'])]
    #[OA\Post(
        path: '/api/price/sum',
        summary: 'The result in case of sum successfully made.',
        tags: [
            'Price Operations'
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'sum_operation',
                    description: 'The result in case of sum successfully made.',
                    value: '{
                            "result": "9p 2s 6d",
                        }',
                    summary: 'The result object in case of sum successfully made'
                )
            ]
        )
    )]
    #[
        OA\Response(
            response: 400,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'invalid_request',
                        summary: 'The result object in case of invalid request',
                        description: 'The result object in case of invalid request',
                        value: '{
                        "status": "Error",
                        "error": "The input has to be the format 18p 16s 1d"
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
                        "error": "Some error occurred during the operations"
                    }'
                    )
                ]
            )
        )
    ]
    #[OA\Parameter(
        name: 'first_price',
        in: 'query',
        description: 'This field represent the first component of the price addition',
        schema: new OA\Schema(type: 'string', description: 'This parameter represents 
        a price in the old British money system (pound, shilling, pence)', example: '5p 17s 8d')

    )]
    #[OA\Parameter(
        name: 'second_price',
        in: 'query',
        description: 'This field represent the first component of the price addition',
        schema: new OA\Schema(type: 'string', description: 'This parameter represents 
        a price in the old British money system (pound, shilling, pence)', example: '3p 4s 10d')

    )]
    public function sum(Request $request, PriceValidator $validator): JsonResponse
    {
        try {
            $firstPrice = $request->get('first_price', null);
            $secondPrice = $request->get('second_price', null);

            //$errors = $validator->validate($request->get('first_price'));
            $errors = $validator->validate($request->get('first_price'));
            $errors = array_merge($errors, $validator->validate($request->get('second_price')));
            if (count($errors) > 0) {
                $errorsString = $errors[0];

                return $this->json([
                    'status'  => 'error',
                    'message' => $errorsString
                ], 400);
            }

            /** Validazione campi in ingresso  */
            $priceService = new PriceService();
            $firstPriceValidated = $priceService->formatPrice($firstPrice);
            $secondPriceValidated = $priceService->formatPrice($secondPrice);
            /******* */

            $sum = $priceService->sumPrices($firstPriceValidated, $secondPriceValidated);

            return $this->json([
                'result' => $sum
            ], 200);
        } catch (Throwable $e) {
            return $this->json([
                'error' => "Some error occurred during the operations"
            ], 500);
        }
    }

    #[Route('/api/price/subtract', name: 'price_sub', methods: ['POST'])]
    #[OA\Post(
        path: '/api/price/subtract',
        summary: 'The result in case of subtraction successfully made.',
        tags: [
            'Price Operations'
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'sub_operation',
                    description: 'The result in case of subtraction successfully made.',
                    value: '{
                            "result": "2p 12s 10d",
                        }',
                    summary: 'The result object in case of subtraction successfully made'
                )
            ]
        )
    )]
    #[
        OA\Response(
            response: 400,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'invalid_request',
                        summary: 'The result object in case of invalid request',
                        description: "The result object in case of invalid request",
                        value: '{
                            "status": "Error",
                            "error":  "The input has to be the format 18p 16s 1d"
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
                        "error": "Some error occurred during the operations"
                    }'
                    )
                ]
            )
        )
    ]
    #[OA\Parameter(
        name: 'first_price',
        in: 'query',
        description: 'This field represent the first component of the price subtraction',
        schema: new OA\Schema(type: 'string', description: 'This parameter represents 
        a price in the old British money system (pound, shilling, pence)', example: '5p 17s 8d')

    )]
    #[OA\Parameter(
        name: 'second_price',
        in: 'query',
        description: 'This field represent the first component of the price subtraction',
        schema: new OA\Schema(type: 'string', description: 'This parameter represents 
        a price in the old British money system (pound, shilling, pence)', example: '3p 4s 10d')

    )]
    public function sub(Request $request, PriceValidator $validator): JsonResponse
    {
        try {
            $firstPrice = $request->getPayload()->get('first_price', null);
            $secondPrice = $request->getPayload()->get('second_price', null);

            $errors = $validator->validate($request->get('first_price'));
            $errors = array_merge($errors, $validator->validate($request->get('second_price')));
            if (count($errors) > 0) {
                $errorsString = $errors[0];

                return $this->json([
                    'status'  => 'error',
                    'message' => $errorsString
                ], 400);
            }

            /** Validazione campi in ingresso  */
            $priceService = new PriceService();
            $firstPriceValidated = $priceService->formatPrice($firstPrice);
            $secondPriceValidated = $priceService->formatPrice($secondPrice);
            /******* */

            $sub = $priceService->subPrices($firstPriceValidated, $secondPriceValidated);

            // Controllo il caso in cui il risultato in cui è minore di 0,
            // in quel caso la richiesta non è valida
            if ($sub == '') {
                return $this->json([
                    "status" => "error",
                    'error' => "Invalid request"
                ], status: 400);
            }

            return $this->json([
                'result' => $sub
            ], 200);
        } catch (Throwable $e) {
            return $this->json([
                'error' => "Some error occurred during the operations"
            ], 500);
        }
    }

    #[Route('/api/price/multiplicate', name: 'price_mul', methods: ['POST'])]
    #[OA\Post(
        path: '/api/price/multiplicate',
        summary: 'The result in case of multiplication successfully made.',
        tags: [
            'Price Operations'
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'mul_operation',
                    description: 'The result in case of multiplication successfully made.',
                    value: '{
                            "result": "11p 15 s 4d",
                        }',
                    summary: 'The result object in case of multiplication successfully made'
                )
            ]
        )
    )]
    #[
        OA\Response(
            response: 400,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'invalid_request',
                        summary: 'The result object in case of invalid request',
                        description: "The result object in case of invalid request",
                        value: '{
                        "status": "error",
                        "message":  "The input has to be the format 18p 16s 1d"
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
                        "error": "Some error occurred during the operations"
                    }'
                    )
                ]
            )
        )
    ]
    #[OA\Parameter(
        name: 'first_price',
        in: 'query',
        description: 'This field represent the first component of the price multiplicaton',
        schema: new OA\Schema(type: 'string', description: 'This parameter represents 
        a price in the old British money system (pound, shilling, pence)', example: '5p 17s 8d')

    )]
    #[OA\Parameter(
        name: 'multiplicator',
        in: 'query',
        description: 'This field represent the multiplication factor. It must be >= 0',
        schema: new OA\Schema(type: 'integer', description: 'This field represent the multiplication factor. It must be >= 0', example: '2')

    )]
    public function multiplicate(Request $request, PriceValidator $validator): Response
    {
        try {
            $firstPrice = $request->getPayload()->get('first_price', null);
            $multiplicator = $request->getPayload()->get('multiplicator', null);

            $errors = $validator->validate($request->get('first_price'));
            if (count($errors) > 0) {
                $errorsString = $errors[0];

                return $this->json([
                    'status'  => 'error',
                    'message' => $errorsString
                ], 400);
            }
            if (gettype($multiplicator) != 'integer' || $multiplicator < 0) {
                return $this->json([
                    "status" => "Error",
                    "message" => "Invalid request, the multiplicatior must be >= 0"
                ], 400);
            }

            /** Validazione campi in ingresso  */
            $priceService = new PriceService();
            $firstPriceValidated = $priceService->formatPrice($firstPrice);

            /******* */

            $mul = $priceService->multiplicatePrices($firstPriceValidated, $multiplicator);

            return $this->json([
                'result' => $mul
            ], 200);
        } catch (Throwable $e) {
            return $this->json([
                'error' => "Some error occurred during the operations"
            ], 500);
        }
    }

    #[Route('/api/price/divide', name: 'price_div', methods: ['POST'])]
    #[OA\Post(
        path: '/api/price/divide',
        summary: 'The result in case of division successfully made.',
        tags: [
            'Price Operations'
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'div_operation',
                    description: 'The result in case of division successfully made.',
                    value: '{
                            "result": "11p 15s 4d",
                        }',
                    summary: 'The result object in case of division successfully made'
                )
            ]
        )
    )]
    #[
        OA\Response(
            response: 400,
            description: 'KO',
            content: new OA\JsonContent(
                examples: [
                    new OA\Examples(
                        example: 'invalid_request',
                        summary: 'The result object in case of invalid request',
                        description: "The result object in case of invalid request",
                        value: '{
                        "status": "Error",
                        "error":  "The input has to be the format 18p 16s 1d"
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
                        "error": "Some error occurred during the operations"
                    }'
                    )
                ]
            )
        )
    ]
    #[OA\Parameter(
        name: 'first_price',
        in: 'query',
        description: 'This field represent the first component of the price division',
        schema: new OA\Schema(type: 'string', description: 'This parameter represents 
        a price in the old British money system (pound, shilling, pence)', example: '18p 16s 1d')

    )]
    #[OA\Parameter(
        name: 'factor',
        in: 'query',
        description: 'This field represent the division factor. It must be > 0',
        schema: new OA\Schema(type: 'integer', description: 'This field represent the division factor. It must be > 0', example: '15')

    )]
    public function divide(Request $request, PriceValidator $validator): Response
    {
        try {
            $firstPrice = $request->getPayload()->get('first_price', null);
            $factor = $request->getPayload()->get('factor', 1);

            $errors = $validator->validate($request->get('first_price'));
            if (count($errors) > 0) {
                $errorsString = $errors[0];

                return $this->json([
                    'status'  => 'error',
                    'message' => $errorsString
                ], 400);
            }
            if (gettype($factor) != 'integer' || $factor <= 0) {
                return $this->json([
                    'status'  => 'error',
                    'message' => "Invalid request"
                ], 400);
            }

            /** Validazione campi in ingresso  */
            $priceService = new PriceService();
            $firstPriceValidated = $priceService->formatPrice($firstPrice);
            /******* */

            $division = $priceService->dividePrices($firstPriceValidated, $factor);

            return $this->json([
                'result'  => $division
            ], 200);
        } catch (Throwable $e) {
            return $this->json([
                'error' => "Some error occurred during the operations"
            ], 500);
        }
    }
}
