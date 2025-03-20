<?php

namespace App\Controller;

use App\Entity\Price;
use App\Service\PriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Config\MonologConfig;
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
    #[OA\Response(
        response: 200,
        description: 'Returns the sum of two prices provided',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Price::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'order',
        in: 'query',
        description: 'The field used to order rewards',
        schema: new OA\Schema(type: 'string')
    )]
    public function sum(Request $request): Response {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $secondPrice = $request->getPayload()->get('second_price', null);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);
        $secondPriceValidated = $priceService->validatePrice($secondPrice);

        $json = new JsonResponse();

        if (sizeof($firstPriceValidated)!=3 || sizeof($secondPriceValidated)!=3) {
            $json->setStatusCode(400, "Invalid request");
        }
        /******* */

        $sum = $priceService->sumPrices($firstPriceValidated, $secondPriceValidated);


        // $firstPriceArray = json_decode($firstPrice, associative: true);
       
        // $resultPrice = new Price();
        // $resultPrice->setPence($firstPricePence);
        // $resultPrice->setShilling($firstPriceShilling);
        // $resultPrice->setPound($firstPricePounds);
        $json->setData(['result'=> $sum]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }

    #[Route('/api/price/sub', name: 'price_sub', methods: ['POST'])]
    public function sub(Request $request): Response {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $secondPrice = $request->getPayload()->get('second_price', null);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);
        $secondPriceValidated = $priceService->validatePrice($secondPrice);

        $json = new JsonResponse();
        if (sizeof($firstPriceValidated)!=3 || sizeof($secondPriceValidated)!=3) {
            $json->setStatusCode(400, "Invalid request");
        }
        /******* */

        $sub = $priceService->subPrices($firstPriceValidated, $secondPriceValidated);

        // Controllo il caso in cui il risultato in cui è minore di 0,
        // in quel caso la richiesta non è valida
        if($sub == '') {
            $json->setStatusCode(400, "Invalid request");
        } 

        // $firstPriceArray = json_decode($firstPrice, associative: true);
       
        // $resultPrice = new Price();
        // $resultPrice->setPence($firstPricePence);
        // $resultPrice->setShilling($firstPriceShilling);
        // $resultPrice->setPound($firstPricePounds);
        $json->setData(['result'=> $sub]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }

    #[Route('/api/price/mul', name: 'price_mul', methods: ['POST'])]
    public function multiplicate(Request $request): Response {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $multiplicator = $request->getPayload()->get('multiplicator', null);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);

        $json = new JsonResponse();
        if(gettype($multiplicator)!='integer' || $multiplicator<0 || sizeof($firstPriceValidated)!=3 ) {
            $json->setStatusCode(400, "Invalid request");
        }
        /******* */

        $mul = $priceService->multiplicatePrices($firstPriceValidated, $multiplicator);

        $json->setData(['result'=> $mul]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }

    #[Route('/api/price/mul', name: 'price_mul', methods: ['POST'])]
    public function divide(Request $request): Response {
        $firstPrice = $request->getPayload()->get('first_price', null);
        $factor = $request->getPayload()->get('factor', null);

        /** Validazione campi in ingresso  */
        $priceService = new PriceService();
        $firstPriceValidated = $priceService->validatePrice($firstPrice);

        $json = new JsonResponse();
        if(gettype($factor)!='integer' || $factor<0 || sizeof($firstPriceValidated)!=3 ) {
            $json->setStatusCode(400, "Invalid request");
        }
        /******* */

        $division = $priceService->dividePrices($firstPriceValidated, $factor);

        $json->setData(['result'=> $division]);
        $json->setStatusCode(200, "Ok");
        return $json;
    }
}
