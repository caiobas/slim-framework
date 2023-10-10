<?php

declare(strict_types=1);

namespace App\Service\StockService;

use App\Entity\StockQuote;
use App\Exception\StockNotValidException;
use App\Exception\UserNotFoundException;
use App\Queue\Email\RabbitEmailSender;
use App\Queue\Email\Template\EmailFromTemplate;
use App\Queue\Email\Template\EmailTemplate;
use App\Repository\StockQuoteRepository;
use App\Repository\UserRepository;
use App\Response\StockResponse;
use Doctrine\ORM\Exception\ORMException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Swift_Mailer;
use Swift_Message;

class StockService
{
    private StooqHttpClient $client;
    private StockQuoteRepository $stockQuoteRepository;
    private UserRepository $userRepository;
    private RabbitEmailSender $emailSender;

    public function __construct(
        StooqHttpClient $client,
        StockQuoteRepository $stockQuoteRepository,
        UserRepository $userRepository,
        RabbitEmailSender $emailSender
    )
    {
        $this->client = $client;
        $this->stockQuoteRepository = $stockQuoteRepository;
        $this->userRepository = $userRepository;
        $this->emailSender = $emailSender;
    }

    /**
     * @throws UserNotFoundException
     * @throws StockNotValidException
     * @throws JsonException|GuzzleException
     * @throws ORMException
     *
     */
    public function getStockQuote(string $stock, int $userId): StockResponse
    {
        $uri = '/q/l/?s='. $stock . '&f=sd2t2ohlcvn&h&e=json';
        $response = $this->client->request('GET', $uri);
        $content = $response->getBody()->getContents();
        $symbols = json_decode($content, false, 512, JSON_THROW_ON_ERROR)->symbols[0];
        if(!property_exists( $symbols , "name" )) {
            throw new StockNotValidException('Stock ' . $symbols->symbol . ' is not valid for search.');
        }
        $user = $this->userRepository->getUserById($userId);

        $stock = (new StockQuote())
            ->setUser($user)
            ->setName($symbols->name)
            ->setSymbol($symbols->symbol)
            ->setOpen($symbols->open)
            ->setClose($symbols->close)
            ->setLow($symbols->low)
            ->setHigh($symbols->high);

        $this->stockQuoteRepository->add($stock);

        $this->sendStockEmail($stock, $user->getEmail());

        return new StockResponse($stock);
    }

    /**
     * @throws JsonException
     */
    private function sendStockEmail(StockQuote $stock, string $email): void
    {
        $message = new EmailTemplate(
            'Stock quote - ' . $stock->getSymbol(),
            new EmailFromTemplate('slimframework@email.io', 'Slim Framework'),
            $email,
            sprintf('Here is your stock quote request:
                
                Symbol: %s
                Name: %s
                Open: %.2f
                Close: %.2f
                High: %.2f
                Low: %.2f
            ',
                $stock->getSymbol(),
                $stock->getName(),
                $stock->getOpen(),
                $stock->getClose(),
                $stock->getHigh(),
                $stock->getLow(),
            )
        );

        $this->emailSender->send($message);
    }
}
