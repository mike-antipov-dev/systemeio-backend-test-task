<?php

namespace App\Validator;

use App\Repository\ProductRepository;
use App\Service\TaxService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ProductPurchaseRequestValidator extends ConstraintValidator
{
    private ProductRepository $productRepository;
    private TaxService $taxService;

    public function __construct(ProductRepository $productRepository, TaxService $taxService)
    {
        $this->productRepository = $productRepository;
        $this->taxService = $taxService;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProductPurchaseRequest) {
            throw new UnexpectedTypeException($constraint, ProductPurchaseRequest::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (empty($value['product'])) {
            $this->context->buildViolation($constraint->productRequiredMessage)
                ->atPath('product')
                ->addViolation();
        } elseif (!is_numeric($value['product'])) {
            $this->context->buildViolation($constraint->productTypeMessage)
                ->atPath('product')
                ->addViolation();
        } elseif (!$this->productRepository->find($value['product'])) {
            $this->context->buildViolation($constraint->productNotFoundMessage)
                ->setParameter('{{ value }}', $value['product'])
                ->atPath('product')
                ->addViolation();
        }

        if (empty($value['taxNumber']) || !$this->taxService->isValidTaxNumber($value['taxNumber'])) {
            $this->context->buildViolation($constraint->taxNumberInvalidMessage)
                ->setParameter('{{ value }}', $value['taxNumber'])
                ->setParameter('{{ countryCode }}', substr($value['taxNumber'], 0, 2))
                ->atPath('taxNumber')
                ->addViolation();
        }

        if (!empty($value['isPurchase']) && $value['isPurchase'] === 1) {
            if (empty($value['paymentProcessor'])) {
                $this->context->buildViolation($constraint->paymentProcessorEmptyMessage)
                    ->atPath('paymentProcessor')
                    ->addViolation();
            } elseif (!in_array($value['paymentProcessor'], $constraint->allowedPaymentProcessors)) {
                $this->context->buildViolation($constraint->paymentProcessorInvalidMessage)
                    ->setParameter('{{ value }}', $value['paymentProcessor'])
                    ->atPath('paymentProcessor')
                    ->addViolation();
            }
        }
    }
}
