<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LoggerService
{
    private const REQUESTS = 'requests';

    /** @var bool */
    private $enabled = false;

    /**
     * LoggerService constructor.
     * @param bool $enabled
     */
    public function __construct(bool $enabled = false)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param string $networkCode
     * @param array $data
     */
    public function log(string $networkCode, array $data): void
    {
        if (!$this->enabled) {
            return;
        }

        if (!isset($data['source_id'])) {
            $data['source_id'] = $this->getRandomID();
        }

        Storage::disk('local')->put(
            self::REQUESTS . '/' . $networkCode . '/' . Carbon::now()->toDateString() . '/' . $data['source_id'] . '.txt',
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    /**
     * @return string
     */
    public function getRandomID(): string
    {
        return 'UNKNOWN_' . rand(1, 1000000000);
    }
}