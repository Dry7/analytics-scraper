<?php

declare(strict_types=1);

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function fixture(string $filename)
    {
        return (array)json_decode(file_get_contents(base_path('tests/fixtures/' . $filename)));
    }

    protected function sleep($seconds)
    {
        sleep($seconds);
    }

    protected function assertVKPostImages(string $expected, string $actual)
    {
        $this->assertEquals($this->clearVKImage($expected), $this->clearVKImage($actual));
    }

    private function clearVKImage(string $image): string
    {
        return preg_replace('#https://[^.]+\.userapi\.com#i', '', $image);
    }
}
