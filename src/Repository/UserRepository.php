<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Exception\EmailAlreadyRegistered;
use App\Exception\UserNotFoundException;
use DateTime;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {

    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        parent::__construct($em, $em->getClassMetadata(User::class));
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws EmailAlreadyRegistered
     */
    public function add(User $user): void
    {
        try {
            $user->setUpdatedAt(new DateTime());
            $this->em->persist($user);
            $this->em->flush();
        } catch(UniqueConstraintViolationException $e) {
            throw new EmailAlreadyRegistered('Email already registered.');
        }
    }

    public function getUserByEmailAndPassword(array $signIn): ?User {
        return $this->findOneBy([
            'email' => $signIn['email'],
            'password' => $signIn['password'],
            'deletedAt' => null
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserById(int $id): User {
        $user = $this->findOneBy([
            'id' => $id,
            'deletedAt' => null
        ]);

        if(is_null($user)) {
            throw new UserNotFoundException('User not found.');
        }

        return $user;
    }
}
