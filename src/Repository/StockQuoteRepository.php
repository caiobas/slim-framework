<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StockQuote;
use App\Exception\EmailAlreadyRegistered;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\EntityRepository;

class StockQuoteRepository extends EntityRepository {

    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        parent::__construct($em, $em->getClassMetadata(StockQuote::class));
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function add(StockQuote $stockQuote): void
    {
        $this->em->persist($stockQuote);
        $this->em->flush();
    }
}
