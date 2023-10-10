<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Doctrine\ORM\Mapping\JoinColumn;

#[ORM\Entity]
#[ORM\Table(name: 'stock_quotes')]
class StockQuote {
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'stocks')]
    private User $user;

    #[ORM\Column(type: Types::STRING)]

    private string $symbol;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::FLOAT)]
    private float $open;

    #[ORM\Column(type: Types::FLOAT)]
    private float $high;

    #[ORM\Column(type: Types::FLOAT)]
    private float $low;

    #[ORM\Column(type: Types::FLOAT)]
    private float $close;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): StockQuote
    {
        $this->user = $user;
        return $this;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): StockQuote
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): StockQuote
    {
        $this->name = $name;
        return $this;
    }

    public function getOpen(): float
    {
        return $this->open;
    }

    public function setOpen(float $open): StockQuote
    {
        $this->open = $open;
        return $this;
    }

    public function getHigh(): float
    {
        return $this->high;
    }

    public function setHigh(float $high): StockQuote
    {
        $this->high = $high;
        return $this;
    }

    public function getLow(): float
    {
        return $this->low;
    }

    public function setLow(float $low): StockQuote
    {
        $this->low = $low;
        return $this;
    }

    public function getClose(): float
    {
        return $this->close;
    }

    public function setClose(float $close): StockQuote
    {
        $this->close = $close;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }
}
