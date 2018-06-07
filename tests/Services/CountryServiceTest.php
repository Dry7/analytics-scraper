<?php

namespace App\Tests\Services;

use App\Services\CountryService;
use Illuminate\Support\Facades\Cache;

class CountryServiceTest extends \TestCase
{
    /** @var CountryService */
    private $service;

    public function setUp()
    {
        $this->service = app(CountryService::class);
    }

    public function countryCodeDataProvider()
    {
        return [
            ['Россия', 'RU'],
            ['Украина', 'UA'],
            ['Белоруссия', 'BY'],
            ['Беларусь', 'BY'],
            ['Азербайджан', 'AZ'],
            ['Кыргызстан', 'KG'],
            ['Молдавия', 'MD'],
            ['Таджикистан', 'TJ'],
            ['Таиланд', 'TH'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider countryCodeDataProvider
     *
     * @param string $address
     * @param string $expected
     */
    public function getCountryCode(string $address, string $expected)
    {
        // act
        $result = $this->service->getCountryCode($address);

        // assert
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function getCountries()
    {
        // act
        $result = $this->service->getCountries();

        // assert
        $this->assertCount(249, $result);

        foreach ($result as $country) {
            $this->assertArrayHasKey('isoCode', $country);
            $this->assertArrayHasKey('name', $country);
            $this->assertNotNull($country['isoCode']);
            $this->assertNotNull($country['name']);
        }
    }

    public function getStatesDataProvider()
    {
        return [
            ['RU', 83],
            ['UA', 27],
            ['BY', 7]
        ];
    }

    /**
     * @test
     *
     * @dataProvider getStatesDataProvider
     *
     * @param string $countryCode
     * @param int $statesCount
     */
    public function getStates(string $countryCode, int $statesCount)
    {
        // act
        $result = $this->service->getStates($countryCode);

        // assert
        $this->assertCount($statesCount, $result);

        foreach ($result as $state) {
            $this->assertArrayHasKey('isoCode', $state);
            $this->assertArrayHasKey('name', $state);
            $this->assertNotNull($state['isoCode']);
            $this->assertNotNull($state['name']);
        }
    }

    public function getCitiesDataProvider()
    {
        return [
            ['RU', 'RU-LIP', 2, 'Липецк'],
            ['RU', 'RU-MOW', 53, 'Москва'],
            ['RU', 'RU-SPE', 17, 'Санкт-Петербург'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider getCitiesDataProvider
     *
     * @param string $countryCode
     * @param string $stateCode
     * @param int $citiesCount
     * @param string $cityName
     */
    public function getCities(string $countryCode, string $stateCode, int $citiesCount, string $cityName)
    {
        // act
        $result = $this->service->getCities($countryCode, $stateCode);

        $city = collect($result)->filter(function ($item) use ($cityName) { return $item['name'] === $cityName; });

        // assert
        $this->assertTrue($city->isNotEmpty());
        $this->assertCount($citiesCount, $result);

        foreach ($result as $state) {
            $this->assertArrayHasKey('geonamesCode', $state);
            $this->assertArrayHasKey('name', $state);
            $this->assertNotNull($state['geonamesCode']);
            $this->assertNotNull($state['name']);
        }
    }

    public function parseAddressDataProvider()
    {
        return [
            ['Санкт-Петербург', 'RU', 'RU-SPE', 498817],
            ['ВШПМ СПбГУПТД (бывш. СЗИП), Джамбула пер., 13, Санкт-Петербург', 'RU', 'RU-SPE', 498817],
            ['Москва', 'RU', 'RU-MOW', 524901],
            ['Хмельницкий, Украина', 'UA', 'UA-68', 706369],
            ['', null, null, null],
        ];
    }

    /**
     * @test
     *
     * @dataProvider parseAddressDataProvider
     *
     * @param string $address
     * @param string $countryCode
     * @param string $stateCode
     * @param int $cityCode
     */
    public function parseAddress(string $address, ?string $countryCode, ?string $stateCode, ?int $cityCode)
    {
        // act
        $result = $this->service->parseAddress($address);

        // assert
        $this->assertEquals([
            'country_code' => $countryCode,
            'state_code' => $stateCode,
            'city_code' => $cityCode,
        ], $result);
    }

    /**
     * @test
     */
    public function findCity()
    {
        // arrange
        $this->createApplication();

        // act
        $result = $this->service->findCity('ВШПМ СПбГУПТД (бывш. СЗИП), Джамбула пер., 13, Санкт-Петербург');

        // assert
        $this->assertEquals(['country_code' => 'RU', 'state_code' => 'RU-SPE', 'city_code' => 498817], $result);
    }
}
