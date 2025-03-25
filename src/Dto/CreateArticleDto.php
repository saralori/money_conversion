<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class CreateArticleDto
{
    public function __construct(
        #[NotBlank(message: 'The code id must be populated')]
        #[Type('string')]
        public readonly string $code_id,

        #[NotBlank(message: 'The name must be populated')]
        #[Type('string')]
        public readonly string $name,

        #[NotBlank(message: 'The name must be populated')]
        #[PositiveOrZero(message: 'The price must be positive')]
        #[Type('float')]
        public readonly float $price,
    ) {
    }
}