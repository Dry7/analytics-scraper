<?php

declare(strict_types=1);

namespace App\Tests\Types;

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
        // act
        $result = Network::getCode($networkId);

        // assert
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function getVkontakteCode()
    {
        // act
        $result = Network::getVkontakteCode();

        // assert
        $this->assertEquals('vk', $result);
    }

    /**
     * @test
     */
    public function getOdnoklassnikiCode()
    {
        // act
        $result = Network::getOdnoklassnikiCode();

        // assert
        $this->assertEquals('ok', $result);
    }

    /**
     * @test
     */
    public function getFacebookCode()
    {
        // act
        $result = Network::getFacebookCode();

        // assert
        $this->assertEquals('fb', $result);
    }

    /**
     * @test
     */
    public function getMailCode()
    {
        // act
        $result = Network::getMailCode();

        // assert
        $this->assertEquals('mail', $result);
    }
}
