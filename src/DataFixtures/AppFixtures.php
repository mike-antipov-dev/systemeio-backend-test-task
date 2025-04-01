<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Coupon;
use App\Entity\TaxRate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $iphone = new Product();
        $iphone->setName('iPhone');
        $iphone->setPrice(100.0);
        $manager->persist($iphone);

        $headphones = new Product();
        $headphones->setName('Наушники');
        $headphones->setPrice(20.0);
        $manager->persist($headphones);

        $case = new Product();
        $case->setName('Чехол');
        $case->setPrice(10.0);
        $manager->persist($case);

        $galaxy = new Product();
        $galaxy->setName('Samsung Galaxy');
        $galaxy->setPrice(100000000.00);
        $manager->persist($galaxy);

        $couponFixed1 = new Coupon();
        $couponFixed1->setCode('F15');
        $couponFixed1->setValue(15);
        $manager->persist($couponFixed1);

        $couponFixed2 = new Coupon();
        $couponFixed2->setCode('F100');
        $couponFixed2->setValue(100);
        $manager->persist($couponFixed2);

        $couponPercentage1 = new Coupon();
        $couponPercentage1->setCode('P6');
        $couponPercentage1->setValue(6);
        $manager->persist($couponPercentage1);

        $couponPercentage2 = new Coupon();
        $couponPercentage2->setCode('P100');
        $couponPercentage2->setValue(100);
        $manager->persist($couponPercentage2);

        $taxRates = [
            [
                'countryCode' => 'DE',
                'country' => 'Germany',
                'percentage' => 19.0,
                'format' => '/^DE\d{9}$/'
            ],
            [
                'countryCode' => 'IT',
                'country' => 'Italy',
                'percentage' => 22.0,
                'format' => '/^IT\d{11}$/'
            ],
            [
                'countryCode' => 'FR',
                'country' => 'France',
                'percentage' => 20.0,
                'format' => '/^FR[A-Z]{2}\d{9}$/'
            ],
            [
                'countryCode' => 'GR',
                'country' => 'Greece',
                'percentage' => 24.0,
                'format' => '/^GR\d{9}$/'
            ],
        ];

        foreach ($taxRates as $data) {
            $taxRate = new TaxRate();
            $taxRate->setCountryCode($data['countryCode']);
            $taxRate->setCountry($data['country']);
            $taxRate->setPercentage($data['percentage']);
            $taxRate->setFormat($data['format']);

            $manager->persist($taxRate);
        }

        $manager->flush();
    }
}
