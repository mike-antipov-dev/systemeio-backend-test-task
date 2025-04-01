<?php

namespace App\Service;

use App\Repository\TaxRateRepository;
use InvalidArgumentException;

/**
 * @description
 * Сервис расчёта налога в зависимости от страны, детали хранятся в таблице tax_rate
 */
class TaxService
{
    private TaxRateRepository $taxRateRepository;

    public function __construct(TaxRateRepository $taxRateRepository)
    {
        $this->taxRateRepository = $taxRateRepository;
    }

    public function getTaxRateFromTaxNumber(string $taxNumber): ?float
    {
        $countryCode = substr($taxNumber, 0, 2);
        $taxRate = $this->taxRateRepository->findByCountryCode($countryCode);

        return $taxRate ? $taxRate->getPercentage() : null;
    }

    public function isValidTaxNumber(string $taxNumber): bool
    {
        // Определяем страну по первым двум символам
        $countryCode = substr($taxNumber, 0, 2);

        // Получаем формат из базы данных
        $taxRate = $this->taxRateRepository->findByCountryCode($countryCode);

        if ($taxRate === null) {
            return false;
        }

        return preg_match($taxRate->getFormat(), $taxNumber) === 1;
    }

    public function calculateTax(float $price, string $taxNumber): float
    {
        $taxRate = $this->getTaxRateFromTaxNumber($taxNumber);

        return $price * ($taxRate / 100);
    }
}
