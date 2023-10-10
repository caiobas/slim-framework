<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\StockQuote;
use DateTime;

class StockResponse  {

    private string $symbol;
    private float $open;
    private float $high;
    private float $low;
    private float $close;
    private string $name;
    private ?DateTime $date;

    public function __construct(StockQuote $stock)
    {
        $this->symbol = $stock->getSymbol();
        $this->open = $stock->getOpen();
        $this->high = $stock->getHigh();
        $this->low = $stock->getLow();
        $this->close = $stock->getClose();
        $this->name = $stock->getName();
        $this->date = $stock->getCreatedAt();
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getOpen(): float
    {
        return $this->open;
    }

    public function getHigh(): float
    {
        return $this->high;
    }

    public function getLow(): float
    {
        return $this->low;
    }

    public function getClose(): float
    {
        return $this->close;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function toArray(bool $hasDate = false): array
    {
        $array = [];
        if($hasDate) {
            $array['date'] = substr($this->date->format('c'), 0, 19);
        }
        return array_merge($array, [
            "name" => $this->name,
            "symbol" => $this->symbol,
            "open" => $this->open,
            "high" => $this->high,
            "low" => $this->low,
            "close" => $this->close
        ]);
    }
}
