<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PurchaseService;

/**
 * @description
 * Контроллер расчета конечной стоимости товара с учётом налога и купона
 */
final class PriceController extends AbstractController
{
    private PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    #[Route('/calculate-price', name: 'calculate_price')]
    public function totalPrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $errors = $this->purchaseService->validateRequest($data);

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $productId = $data['product'] ?? null;
        $taxNumber = $data['taxNumber'] ?? null;
        $couponCode = $data['couponCode'] ?? null;

        $priceDetails = $this->purchaseService->calculatePrice($productId, $taxNumber, $couponCode);

        return $this->json($priceDetails);
    }
}
