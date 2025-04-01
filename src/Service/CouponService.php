<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Repository\CouponRepository;

/**
 * @description
 * Сервис обработки купонов
 */
class CouponService
{
    private CouponRepository $couponRepository;

    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    public function findCoupon(?string $couponCode): ?Coupon
    {
        if (!$couponCode) {
            return null;
        }

        return $this->couponRepository->findByCode($couponCode);
    }

    public function calculateDiscount(float $price, ?string $couponCode): float
    {
        $coupon = $this->findCoupon($couponCode);

        if (!$coupon) {
            return 0;
        }

        // TODO: Решить что делать с купонами, которые больше номиналом, чем стоимость товара
        if ($coupon->isFixed()) {
            return min($coupon->getValue(), $price);
        } else {
            return $price * ($coupon->getValue() / 100);
        }
    }

    public function applyDiscount(float $price, ?string $couponCode): float
    {
        $discount = $this->calculateDiscount($price, $couponCode);

        return $price - $discount;
    }
}
