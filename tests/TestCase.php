<?php

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
}
