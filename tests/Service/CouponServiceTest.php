<?php

namespace App\Tests\Service;

use App\Entity\Coupon;
use App\Repository\CouponRepository;
use App\Service\CouponService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CouponServiceTest extends TestCase
{
    private CouponService $couponService;
    private MockObject $couponRepository;

    protected function setUp(): void
    {
        $this->couponRepository = $this->createMock(CouponRepository::class);
        $this->couponService = new CouponService($this->couponRepository);
    }

    public function testApplyDiscountWithInvalidCode()
    {
        $invalidCoupon = (new Coupon())->setCode('')->setValue(15);
        $this->couponRepository
            ->method('findByCode')
            ->with('')
            ->willReturn($invalidCoupon);

        $this->assertEquals(100.0, $this->couponService->applyDiscount(100.0, ''));
    }

    public function testApplyDiscountWithValidCode()
    {
        $validCoupon = (new Coupon())->setCode('F15')->setValue(15);
        $this->couponRepository
            ->method('findByCode')
            ->with('F15') // Match the valid code
            ->willReturn($validCoupon);

        $this->assertEquals(85.0, $this->couponService->applyDiscount(100.0, 'F15'));
    }

    public function testApplyPercentageDiscount()
    {
        $coupon = (new Coupon())->setCode('P6')->setValue(6.0);
        $this->couponRepository
            ->method('findByCode')
            ->willReturn($coupon);

        $this->assertEquals(94.0, $this->couponService->applyDiscount(100.0, 'P6'));
    }
}
