<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Validator\ProductPurchaseRequest;
use InvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

/**
 * @description
 * Сервис обработки покупки товаров
 */
class PurchaseService
{
    private ProductRepository $productRepository;
    private ValidatorInterface $validator;
    private TaxService $taxService;
    private CouponService $couponService;

    public function __construct(
        ProductRepository $productRepository,
        ValidatorInterface $validator,
        TaxService $taxService,
        CouponService $couponService
    ) {
        $this->productRepository = $productRepository;
        $this->validator = $validator;
        $this->taxService = $taxService;
        $this->couponService = $couponService;
    }

    public function validateRequest(array $data): array
    {
        $constraint = new ProductPurchaseRequest();
        $requestViolations = $this->validator->validate($data, $constraint);
        $errors = [];

        foreach ($requestViolations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = $violation->getMessage();
        }

        return $errors;
    }

    public function calculatePrice(int $productId, string $taxNumber, string $couponCode): array
    {
        $product = $this->productRepository->find($productId);
        $productPrice = (float) $product->getPrice();
        $discountedPrice = $this->couponService->applyDiscount($productPrice, $couponCode);
        $tax = $this->taxService->calculateTax($discountedPrice, $taxNumber);
        $totalPrice = $discountedPrice + $tax;

        return [
            'productName' => $product->getName(),
            'originalPrice' => $productPrice,
            'discount' => $productPrice - $discountedPrice,
            'priceAfterDiscount' => $discountedPrice,
            'tax' => $tax,
            'totalPrice' => $totalPrice
        ];
    }

    /**
     * @description
     * Список обработчиков платежей, StripePaymentProcessor возвращает 1 или 0,
     * PaypalPaymentProcessor возвращает void или Exception
     *
     * @throws \Exception
     */
    public function processPayment(string $processor, float $amount): bool
    {
        $processor = strtolower($processor);

        switch ($processor) {
            case 'paypal':
                $paypalProcessor = new PaypalPaymentProcessor();
                $paypalProcessor->pay($amount);

                return true;

            case 'stripe':
                $stripeProcessor = new StripePaymentProcessor();
                return $stripeProcessor->processPayment($amount);

            default:
                return false;
        }
    }
}
