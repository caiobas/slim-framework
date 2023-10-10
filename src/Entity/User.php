<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User {
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $email;

    #[ORM\Column(type: Types::STRING)]
    private string $password;

    #[ORM\Column(name: 'first_name', type: Types::STRING)]
    private string $firstName;

    #[ORM\Column(name: 'last_name', type: Types::STRING)]
    private string $lastName;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $updatedAt = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $deletedAt = null;

    /**
     * @var Collection<int, StockQuote>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: StockQuote::class)]
    #[ORM\OrderBy(["id" => "DESC"])]
    private Collection $stocks;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        if(!empty($email)) {
            $this->email = $email;
        }
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        if(!empty($password)) {
            $this->password = crypt($password, 'encr1pt10n');
        }
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        if(!empty($firstName)) {
            $this->firstName = $firstName;
        }
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): User
    {
        if(!empty($lastName)) {
            $this->lastName = $lastName;
        }
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): User
    {
        if(is_null($this->createdAt)) {
            $this->createdAt = $updatedAt;
        }
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @return Collection<int, StockQuote>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * @param Collection<int, StockQuote> $stocks
     */
    public function setStocks(Collection $stocks): User
    {
        $this->stocks = $stocks;
        return $this;
    }
}
