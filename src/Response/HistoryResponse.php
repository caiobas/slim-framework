<?php

declare(strict_types=1);

namespace App\Response;

class HistoryResponse  {


    /**
     * @var array<StockResponse>
     */
    private array $stocks;

    /**
     * @param array<StockResponse> $stocksResponse
     */
    public function __construct(array $stocksResponse)
    {
        $this->stocks = $stocksResponse;
    }

    public function getStocks(): array
    {
        return $this->stocks;
    }

    public function toArray(): array
    {
        $array = [];

        foreach ($this->stocks as $stock) {
            $array[] = $stock->toArray(true);
        }

        return $array;
    }
}
