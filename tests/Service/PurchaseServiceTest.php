<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PurchaseService;
use App\Service\CouponService;
use App\Service\TaxService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PurchaseServiceTest extends TestCase
{
    private PurchaseService $purchaseService;
    private MockObject $productRepository;
    private MockObject $couponService;
    private MockObject $taxService;
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->couponService = $this->createMock(CouponService::class);
        $this->taxService = $this->createMock(TaxService::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->purchaseService = new PurchaseService(
            $this->productRepository,
            $this->validator,
            $this->taxService,
            $this->couponService
        );
    }

    public function testCalculatePriceWithValidData()
    {
        $product = (new Product())->setName('iPhone')->setPrice('100.00');
        $this->productRepository->method('find')->willReturn($product);
        $this->couponService->method('applyDiscount')->willReturn(85.0);
        $this->taxService->method('calculateTax')->willReturn(19.0);

        $result = $this->purchaseService->calculatePrice(1, 'GR123456789', 'F15');

        $this->assertEquals([
            'productName' => 'iPhone',
            'originalPrice' => 100.0,
            'discount' => 15.0,
            'priceAfterDiscount' => 85.0,
            'tax' => 19.0,
            'totalPrice' => 104.0
        ], $result);
    }

    public function testProcessPaymentWithStripeSuccess()
    {
        $result = $this->purchaseService->processPayment('stripe', 100.0);
        $this->assertTrue($result);
    }

    public function testProcessPaymentWithStripeFailure()
    {
        $result = $this->purchaseService->processPayment('stripe', 0.5);
        $this->assertFalse($result);
    }

    public function testProcessPaymentWithPaypalSuccess()
    {
        $this->expectNotToPerformAssertions();
        $this->purchaseService->processPayment('paypal', 1.00);
    }

    public function testProcessPaymentWithPaypalFailure()
    {
        $this->expectException(\Exception::class);
        $this->purchaseService->processPayment('paypal', 9999999.99);
    }
}
