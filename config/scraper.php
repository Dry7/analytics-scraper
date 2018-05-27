<?php

return [
    'api_key' => env('SCRAPER_API_KEY'),
    'ips' => explode(',', env('SCRAPER_IPS', '')),
];