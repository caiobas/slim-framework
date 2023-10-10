<?php

declare(strict_types=1);

namespace App\Service\StockService;

use GuzzleHttp\Client;

class StooqHttpClient extends Client
{
    public function __construct(string $baseUrl)
    {
        parent::__construct([
            'base_uri' => $baseUrl,
        ]);
    }
}
