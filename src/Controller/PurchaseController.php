<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PurchaseService;
use Exception;

/**
 * @description
 * Контроллер обработки покупки товара
 */
final class PurchaseController extends AbstractController
{
    private PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    #[Route('/purchase', name: 'purchase_product')]
    public function purchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $data['isPurchase'] = 1;
        $errors = $this->purchaseService->validateRequest($data);

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $productId = $data['product'];
        $taxNumber = $data['taxNumber'];
        $couponCode = $data['couponCode'] ?? null;
        $paymentProcessor = $data['paymentProcessor'];

        try {
            $purchaseData = $this->purchaseService->calculatePrice($productId, $taxNumber, $couponCode);
            $totalPrice = $purchaseData['totalPrice'];
            $paymentSuccess = $this->purchaseService->processPayment($paymentProcessor, $totalPrice);

            if ($paymentProcessor === 'stripe' && !$paymentSuccess) {
                return $this->json([
                    'errors' => ['payment' => 'Не удалось обработать платеж, итоговая сумма должна быть не менее 100.']
                ], Response::HTTP_BAD_REQUEST);
            }

            return $this->json([
                'success' => true,
                'message' => 'Платеж успешно выполнен',
                'paymentMethod' => $paymentProcessor,
                'details' => $purchaseData
            ]);
        } catch (Exception $e) {
            return $this->json([
                'errors' => ['payment' => $e->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
