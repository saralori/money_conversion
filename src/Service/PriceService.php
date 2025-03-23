<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;

class PriceService
{
    public function validatePrice(string $price): array
    {
        $parameters = explode(" ", $price);

        if (sizeof($parameters) != 3) {
            return ["Error on parameters format"];
        }

        $pounds = $this->formatSinglePriceVoices($parameters[0], 'pound');
        $shillings = $this->formatSinglePriceVoices($parameters[1], 'shilling');
        $pences = $this->formatSinglePriceVoices($parameters[2], 'pence');

        if ($pounds == '' || $shillings == '' || $pences == '') {
            return ['Error on parameters format'];
        }
        //TODO: gestire validazione campi interi

        return [$pounds, $shillings, $pences];
    }

    public function validateCoefficient($coeff): bool
    {
        $error = false;
        if (gettype($coeff) != 'integer' || $coeff < 0) {
            $error = true;
        }
        return $error;
    }

    public function sumPrices(array $firstPrice, array $secondPrice): string
    {
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

    public function subPrices(array $firstPrice, array $secondPrice): string
    {
        $sub = [];
        //Converto i prezzi in pence, così da facilitare i conti
        $firstPriceConverted = $this->convertPriceToPence($firstPrice);
        $secondPriceConverted = $this->convertPriceToPence($secondPrice);

        //Sottraggo i prezzi in pence
        $subConverted = $firstPriceConverted - $secondPriceConverted;

        // Se la sottrazione dà risultato negativo restituisco stringa vuota
        // per gestire l'errore
        if ($subConverted < 0) {
            return '';
        }

        //Converto il prezzo in singole unità
        $subArray = $this->convertPriceToSingleUnits($subConverted);

        //Trasformo la somma in strings con le unità di misura
        $sub = $this->convertWithStringWithMeasureUnits($subArray);
        return $sub;
    }

    public function multiplicatePrices(array $price, int $multiplicator): string
    {
        $mul = [];
        //Converto i prezzi in pence, così da facilitare i conti
        $priceConverted = $this->convertPriceToPence($price);

        //Sommo i prezzi in pence
        $mulConverted = $priceConverted * $multiplicator;

        //Converto il prezzo in singole unità
        $mulArray = $this->convertPriceToSingleUnits($mulConverted);

        //Trasformo la somma in strings con le unità di misura
        $mul = $this->convertWithStringWithMeasureUnits($mulArray);
        return $mul;
    }

    public function dividePrices(array $price, int $factor): string
    {
        $divisionArray = [];
        //Converto i prezzi in pence, così da facilitare i conti
        $priceConverted = $this->convertPriceToPence($price);

        //Divido i prezzi in pence, considerando solo la divisione intera
        $divisionConverted = intdiv($priceConverted, $factor);
        //Calcolo il resto
        $moduleConverted = $priceConverted % $factor;

        //Converto il prezzo in singole unità
        $divisionArray = $this->convertPriceToSingleUnits($divisionConverted);
        $moduleArray = $this->convertPriceToSingleUnits($moduleConverted);

        //Trasformo la somma in strings con le unità di misura
        $division = $this->convertWithStringWithMeasureUnits($divisionArray);
        $module = $this->convertWithStringWithMeasureUnits($moduleArray);

        return $division . " (" . $module . ")";
    }

    private function formatSinglePriceVoices(string $voice, $voiceType): string
    {
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
        if (
            sizeof($value) != 2 ||
            strlen($value[1]) != 1 ||
            gettype($value[1]) != 'integer' ||
            $value[1] < 0
        ) {
            return '';
        }

        return $value[0];
    }

    private function convertPriceToPence(array $price): int
    {
        //Converto i pounds in shilling
        $shillings = 20 * $price[0] + $price[1];
        //Converto gli shilling in pence
        $pences = 12 * $shillings + $price[2];

        return $pences;
    }

    private function convertPriceToSingleUnits(int $price): array
    {
        //Inizializzo le variabili
        $pounds = 0;
        $shillings = 0;
        $pences = 0;

        // Controllo se rientro nei 12 pence
        if ($price < 12) {
            return [$pounds, $shillings, $price];
        }
        $shillings = intdiv($price, 12);
        $pences = $price % 12;

        // Controllo se rientro nei 20 shilling
        if ($shillings < 20) {
            return [$pounds, $shillings, $pences];
        }

        $pounds = intdiv($shillings, 20);
        $shillings = $shillings % 20;
        return [$pounds, $shillings, $pences];
    }

    private function convertWithStringWithMeasureUnits(array $values): string
    {
        $price = '';
        $measureUnits = ["p", "s", "d"];
        for ($i = 0; $i < 3; $i++) {
            $price .= $values[$i] . $measureUnits[$i] . " ";
        }
        return trim($price);
    }
}
