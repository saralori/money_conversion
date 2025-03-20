<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;

class PriceService
{
    public function validatePrice(string $price): array
    {
        $parameters = explode(" ", $price);

        if(sizeof($parameters)!=3) {
            return [];
        }
        
        $pounds = $this->formatSinglePriceVoices($parameters[0], 'pound');
        $shillings = $this->formatSinglePriceVoices($parameters[1], 'shilling');
        $pences = $this->formatSinglePriceVoices($parameters[2], 'pence');

        if($pounds == '' || $shillings == '' || $pences == '') {
            return [];
        }
        //TODO: gestire validazione campi interi

        return [$pounds, $shillings, $pences];
    }

    public function sumPrices(array $firstPrice, array $secondPrice): string {
        $sum = [];
        //Converto i prezzi in pence, così da facilitare i conti
        $firstPriceConverted = $this->convertPriceToPence($firstPrice);
        $secondPriceConverted = $this->convertPriceToPence($secondPrice);

        //Sommo i prezzi in pence
        $sumConverted = $firstPriceConverted + $secondPriceConverted;

        //Converto il prezzo in singole unità
        $sumArray = $this->convertPriceToSingleUnits($sumConverted);

        //Trasformo la somma in strings con le unità di misura
        $sum = $this->convertWithStringWithMeasureUnits($sumArray);
        return $sum;
    }

    private function formatSinglePriceVoices(string $voice, $voiceType): string {
        $value = '';
        switch ($voiceType) {
            case 'pound':
                $value = explode('p', $voice);
                break;
            case 'shilling':
                $value = explode('s', $voice);
                break;
            case 'pence':
                $value = explode('d', $voice);
                break;
            default:
                //In caso di errore setto $value come array vuoto, così da gestirlo come dato anomalo
                $value = [];
        }
        if(sizeof($value)!=2) {
            return '';
        }
        return $value[0];
    }

    private function convertPriceToPence(array $price): int {
        //Converto i pounds in shilling
        $shillings = 20 * $price[0] + $price[1];
        //Converto gli shilling in pence
        $pences = 12 * $shillings + $price[2];

        return $pences;
    }

    private function convertPriceToSingleUnits(int $price): array {
        //Inizializzo le variabili
        $pounds = 0;
        $shillings = 0;
        $pences = 0;
        // Controllo se rientro nei 12 pence
        if($price <12) {
            return [$pounds, $shillings, $price];
        }
        $shillings = intdiv($price, 12);
        $pences = $price % 12;

        // Controllo se rientro nei 20 shilling
        if($shillings < 20) {
            return [$pounds, $shillings, $pences];
        }

        $pounds = intdiv($shillings,20);
        $shillings = $shillings % 20;
        return [$pounds, $shillings, $pences];
    }

    private function convertWithStringWithMeasureUnits(array $values): string {
        $price = '';
        $measureUnits = ["p", "s", "d"];
        for($i=0; $i<3; $i++) {
            $price .= $values[$i] . $measureUnits[$i] . " ";
        }
        return trim($price);
    }
}

?>