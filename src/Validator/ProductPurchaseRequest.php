<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class ProductPurchaseRequest extends Constraint
{
    public string $productNotFoundMessage = 'Товар с ID "{{ value }}" не найден.';
    public string $productRequiredMessage = 'ID продукта обязателен.';
    public string $productTypeMessage = 'ID продукта должен быть числом.';
    public string $taxNumberInvalidMessage = 'Налоговый номер "{{ value }}" введён неверно.';
    public string $paymentProcessorInvalidMessage = 'Платежный метод "{{ value }}" не поддерживается.';
    public string $paymentProcessorEmptyMessage = 'Необходимо указать способ оплаты.';
    public array $allowedPaymentProcessors = ['paypal', 'stripe'];
}
