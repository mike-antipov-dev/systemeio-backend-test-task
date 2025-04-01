<?php
namespace App\Tests\Service;

use App\Entity\TaxRate;
use App\Repository\TaxRateRepository;
use App\Service\TaxService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class TaxServiceTest extends TestCase
{
    private TaxService $taxService;
    private MockObject $taxRateRepository;

    protected function setUp(): void
    {
        $this->taxRateRepository = $this->createMock(TaxRateRepository::class);
        $this->taxService = new TaxService($this->taxRateRepository);
    }

    public function testIsValidTaxNumber()
    {
        $taxRate = (new TaxRate())->setFormat('/^DE\d{9}$/');
        $this->taxRateRepository->method('findByCountryCode')->willReturn($taxRate);

        $this->assertTrue($this->taxService->isValidTaxNumber('DE123456789'));
        $this->assertFalse($this->taxService->isValidTaxNumber('DE123'));
    }

    public function testCalculateTax()
    {
        $taxRate = (new TaxRate())->setPercentage(19.0);
        $this->taxRateRepository->method('findByCountryCode')->willReturn($taxRate);

        $this->assertEquals(19.0, $this->taxService->calculateTax(100.0, 'DE123456789'));
    }
}
