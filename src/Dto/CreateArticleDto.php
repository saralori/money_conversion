<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateArticleDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $code_id,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $name,

        #[Assert\PositiveOrZero]
        #[Assert\Type('float')]
        public readonly float $price,
    ) {
    }
}