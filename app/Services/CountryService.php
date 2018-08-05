<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use MenaraSolutions\Geographer\Earth;
use MenaraSolutions\Geographer\Services\TranslationAgency;
use MenaraSolutions\Geographer\State;

class CountryService
{
    private const CACHE_KEY_FIND_CITY = 'CountryService::findCity';

    /** @var Earth */
    private $service;

    public function __construct(Earth $service)
    {
        $this->service = $service->setLocale(TranslationAgency::LANG_RUSSIAN);
    }

    /**
     * @param string $address
     * @return null|string
     */
    public function getCountryCode(string $address): ?string
    {
        $address = preg_replace('#Беларусь#i', 'Белоруссия', $address);

        foreach ($this->service->getCountries() as $country) {
            if (preg_match('#' . $country->name . '#i', $address)) {
                return $country->isoCode;
            }
        }

        return null;
    }

    /**
     * @param string $address
     * @return array
     */
    public function findCity(string $address)
    {
        return Cache::rememberForever(self::CACHE_KEY_FIND_CITY . '::' . md5($address), function () use ($address) {
            return $this->parseAddress($address);
        });
    }

    public function parseAddress(string $address)
    {
        $countryCode = $this->getCountryCode($address) ?? 'RU';
        $stateCode = null;

        foreach ([$countryCode, 'RU', 'UA', 'BY'] as $country) {
            $city = $this->findStateAndCity($address, $country, $stateCode);

            if (!is_null($city)) {
                return $city;
            }
        }
//        foreach($this->service->findOneByCode($countryCode)->getStates() as $state) {
//            if (preg_match('#' . $state->name . '#i', $address)) {
//                $stateCode = $state->isoCode;
//            }
//            foreach ($state->getCities() as $city) {
//                if (preg_match('#' . $city->name . '#i', $address)) {
//                    return [
//                        'country_code' => $countryCode,
//                        'state_code' => $state->isoCode,
//                        'city_code' => $city->geonamesCode,
//                    ];
//                }
//            }
//        }

        return [
            'country_code' => $this->getCountryCode($address),
            'state_code' => $stateCode,
            'city_code' => null,
        ];
    }

    private function findStateAndCity(string $address, ?string $countryCode, ?string &$stateCode)
    {
        foreach($this->service->findOneByCode($countryCode)->getStates() as $state) {
            if (preg_match('#' . $state->name . '#i', $address)) {
                $stateCode = $state->isoCode;
            }
            foreach ($state->getCities() as $city) {
                if (preg_match('#' . $city->name . '#i', $address)) {
                    return [
                        'country_code' => $countryCode,
                        'state_code' => $state->isoCode,
                        'city_code' => $city->geonamesCode,
                    ];
                }
            }
        }

        return null;
    }

    public function getCountries()
    {
        return collect($this->service->getCountries())
            ->map(function ($item) { return ['isoCode' => $item->isoCode, 'name' => $item->name]; })
            ->sortBy('name')
            ->values();
    }

    public function getStates(string $countryCode)
    {
        return collect($this->service->findOneByCode($countryCode)->getStates())
            ->map(function ($item) { return ['isoCode' => $item->isoCode, 'name' => $item->name]; })
            ->sortBy('name')
            ->values();
    }

    public function getCities(string $countryCode, string $stateCode)
    {
        return collect(State::build($stateCode)->setLocale(TranslationAgency::LANG_RUSSIAN)->getCities())
            ->map(function ($item) { return ['geonamesCode' => $item->geonamesCode, 'name' => $item->name]; })
            ->sortBy('name')
            ->values();
    }
}