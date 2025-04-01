<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 15)]
    private string $couponCode;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->couponCode;
    }

    public function setCode(string $code): static
    {
        $this->couponCode = $code;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function isFixed(): bool
    {
        return strtoupper(substr($this->couponCode, 0, 1)) === 'F';
    }
}
