<?php

namespace App\Tests\Services;

use App\Types\Network;

class NetworkTest extends \TestCase
{
    public function getCodeDataProvider()
    {
        return [
            [1, 'vk'],
            [2, 'ok'],
            [3, 'fb'],
            [4, 'mail'],
            [5, null],
        ];
    }

    /**
     * @test
     *
     * @dataProvider getCodeDataProvider
     *
     * @param int $networkId
     * @param string $expected
     */
    public function getCode(int $networkId, ?string $expected)
    {
        $result = Network::getCode($networkId);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function getVkontakteCode()
    {
        $result = Network::getVkontakteCode();

        $this->assertEquals('vk', $result);
    }

    /**
     * @test
     */
    public function getOdnoklassnikiCode()
    {
        $result = Network::getOdnoklassnikiCode();

        $this->assertEquals('ok', $result);
    }

    /**
     * @test
     */
    public function getFacebookCode()
    {
        $result = Network::getFacebookCode();

        $this->assertEquals('fb', $result);
    }

    /**
     * @test
     */
    public function getMailCode()
    {
        $result = Network::getMailCode();

        $this->assertEquals('mail', $result);
    }
}
