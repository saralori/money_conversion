<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $pence = null;

    #[ORM\Column]
    private ?int $shilling = null;

    #[ORM\Column]
    private ?int $pound = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPence(): ?int
    {
        return $this->pence;
    }

    public function setPence(int $pence): static
    {
        $this->pence = $pence;

        return $this;
    }

    public function getShilling(): ?int
    {
        return $this->shilling;
    }

    public function setShilling(int $shilling): static
    {
        $this->shilling = $shilling;

        return $this;
    }

    public function getPound(): ?int
    {
        return $this->pound;
    }

    public function setPound(int $pound): static
    {
        $this->pound = $pound;

        return $this;
    }
}
