<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\EmailAlreadyRegistered;
use App\Exception\InvalidParametersException;
use App\Exception\UnauthenticatedException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Response\HistoryResponse;
use App\Response\SignInResponse;
use App\Response\StockResponse;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JsonException;

class UserService
{
    private UserRepository $repository;
    private AuthenticationService $authenticator;

    public function __construct(UserRepository $repository, AuthenticationService $authenticator)
    {
        $this->repository = $repository;
        $this->authenticator = $authenticator;
    }

    /**
     * @throws OptimisticLockException
     * @throws InvalidParametersException
     * @throws ORMException|EmailAlreadyRegistered
     */
    public function signUp(object $body): void {
        try {
            $user = (new User())
                ->setEmail($body->email)
                ->setPassword($body->password)
                ->setFirstName($body->first_name)
                ->setLastName($body->last_name);
        } catch(\Throwable $e) {
            throw new InvalidParametersException($e->getMessage());
        }

        $this->repository->add($user);
    }

    /**
     * @throws InvalidParametersException
     * @throws JsonException
     * @throws UnauthenticatedException
     */
    public function signIn(object $body): SignInResponse {
        try {
            $signIn = [
                'email' =>$body->email,
                'password' => crypt($body->password, 'encr1pt10n')];
        } catch(\Throwable $e) {
            throw new InvalidParametersException($e->getMessage());
        }
        $user = $this->repository->getUserByEmailAndPassword($signIn);
        if(is_null($user)){
            throw new UnauthenticatedException('Email or password is incorrect.');
        }
        $token = $this->authenticator->generateToken($user);
        return (new SignInResponse($token));
    }

    /**
     * @throws UserNotFoundException
     */
    public function history(int $userId): HistoryResponse
    {
        $user = $this->repository->getUserById($userId);

        $stocks = $user->getStocks()->toArray();

        $stocksResponse = [];

        foreach ($stocks as $stock) {
            $stocksResponse[] = new StockResponse($stock);
        }

        return new HistoryResponse($stocksResponse);
    }
}
