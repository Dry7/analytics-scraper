<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\Utils;

class UtilsTest extends \TestCase
{
    public function string2nullDataProvider()
    {
        return [
            ['Строка', 'Строка'],
            ['', null],
            [0, 0],
        ];
    }

    /**
     * @test
     *
     * @dataProvider string2nullDataProvider
     *
     * @param string $value
     * @param string $expected
     */
    public function string2null(string $value, ?string $expected)
    {
        // act
        $result = Utils::string2null($value);

        // assert
        $this->assertEquals($expected, $result);
    }

    public function randomArrayValueDataProvider()
    {
        return [
            [[1], 1],
            [['String'], 'String'],
            [[2, 2, 2, 2], 2],
            [[], null],
        ];
    }

    /**
     * @test
     *
     * @dataProvider randomArrayValueDataProvider
     *
     * @param array $array
     * @param mixed $expected
     */
    public function randomArrayValue(array $array, $expected)
    {
        // act
        $result = Utils::randomArrayValue($array);

        // assert
        $this->assertEquals($expected, $result);
    }
}
