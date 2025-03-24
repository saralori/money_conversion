<?php

namespace App\Controller;

use App\Service\PriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
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
        ])]
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
                        "error": "The input has to be the format 18p 16s 1d"
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
    public function sum(Request $request): Response
    {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $secondPrice = $request->getPayload()->get('second_price', null);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);
        $secondPriceValidated = $priceService->validatePrice($secondPrice);

        $json = new JsonResponse();

        if (sizeof($firstPriceValidated) != 3 || sizeof($secondPriceValidated) != 3) {
            $json->setStatusCode(400, "Invalid request");
            $json->setData(["error" => "The input has to be the format '18p 16s 1d'"]);
            return $json;
        }
        /******* */

        $sum = $priceService->sumPrices($firstPriceValidated, $secondPriceValidated);

        $json->setData(['result' => $sum]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }

    #[Route('/api/price/subtract', name: 'price_sub', methods: ['POST'])]
    #[OA\Post(
        path: '/api/price/subtract',
        summary: 'The result in case of subtraction successfully made.',
        tags: [
            'Price Operations'
        ])]
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
                            "error":  "The input has to be the format 18p 16s 1d"
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
    public function sub(Request $request): Response
    {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $secondPrice = $request->getPayload()->get('second_price', null);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);
        $secondPriceValidated = $priceService->validatePrice($secondPrice);

        $json = new JsonResponse();
        if (sizeof($firstPriceValidated) != 3 || sizeof($secondPriceValidated) != 3) {
            $json->setData(["error" => "The input has to be the format '18p 16s 1d'"]);
            $json->setStatusCode(400, "Invalid request");
        }
        /******* */

        $sub = $priceService->subPrices($firstPriceValidated, $secondPriceValidated);

        // Controllo il caso in cui il risultato in cui è minore di 0,
        // in quel caso la richiesta non è valida
        if ($sub == '') {
            $json->setStatusCode(400, "Invalid request");
        }

        // $firstPriceArray = json_decode($firstPrice, associative: true);

        // $resultPrice = new Price();
        // $resultPrice->setPence($firstPricePence);
        // $resultPrice->setShilling($firstPriceShilling);
        // $resultPrice->setPound($firstPricePounds);
        $json->setData(['result' => $sub]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }

    #[Route('/api/price/multiplicate', name: 'price_mul', methods: ['POST'])]
    #[OA\Post(
        path: '/api/price/multiplicate',
        summary: 'The result in case of multiplication successfully made.',
        tags: [
            'Price Operations'
        ])]
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
                        "error":  "The input has to be the format 18p 16s 1d"
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
    public function multiplicate(Request $request): Response
    {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $multiplicator = $request->getPayload()->get('multiplicator', null);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);

        $json = new JsonResponse();
        if (gettype($multiplicator) != 'integer' || $multiplicator < 0 || sizeof($firstPriceValidated) != 3) {
            $json->setStatusCode(400, "Invalid request");
        }
        /******* */

        $mul = $priceService->multiplicatePrices($firstPriceValidated, $multiplicator);

        $json->setData(['result' => $mul]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }

    #[Route('/api/price/divide', name: 'price_div', methods: ['POST'])]
    #[OA\Post(
        path: '/api/price/divide',
        summary: 'The result in case of division successfully made.',
        tags: [
            'Price Operations'
        ])]
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
                        "error":  "The input has to be the format 18p 16s 1d"
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
    public function divide(Request $request): Response
    {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $factor = $request->getPayload()->get('factor', 1);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);
        $json = new JsonResponse();
        if (gettype($factor) != 'integer' || $factor <= 0 || sizeof($firstPriceValidated) != 3) {
            $json->setStatusCode(400, "Invalid request");
        }
        /******* */

        $division = $priceService->dividePrices($firstPriceValidated, $factor);

        $json->setData(['result' => $division]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }
}
