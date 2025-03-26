<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PriceValidator
{
    public function validate($value): array
    {
        if (null === $value || '' === $value) {
            return ["The value must be not empty"];
        }

        if (!is_string($value)) {
            return ["The value must be a string"];
        }

        $parameters = explode(" ", $value);

        if (sizeof($parameters) != 3) {
            return ["The format must be 5p 17s 8d "];
        }

        $currency = ['pound', 'shilling', 'pence'];
        for($i=0; $i<3; $i++) {
            if ($this->checkSinglePriceVoices($parameters[$i], $currency[$i]) == false) {
                return ["The format must be 5p 17s 8d "];
            }
        }
        return [];
    }

    private function checkSinglePriceVoices(string $voice, $voiceType): bool
    {
        $valid = false;
        switch ($voiceType) {
            case 'pound':
                if (preg_match('/^\d*p$/', $voice)) {
                    $valid = true;
                }
                break;
            case 'shilling':
                if (preg_match('/^\d*s$/', $voice)) {
                    $valid = true;
                }
                break;
            case 'pence':
                if (preg_match('/^\d*d$/', $voice)) {
                    $valid = true;
                }
                break;
            default:
                //In caso di errore setto $value come array vuoto, così da gestirlo come dato anomalo
                $valid = false;
        }

        return $valid;
    }
}
