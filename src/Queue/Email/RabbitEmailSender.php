<?php

declare(strict_types=1);

namespace App\Queue\Email;


use App\Queue\Email\Template\EmailTemplate;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitEmailSender
{
    public const QUEUE = 'email_sender';
    private AMQPChannel $channel;

    public function __construct(AMQPStreamConnection $amqpConn)
    {
        $this->channel = $amqpConn->channel();
        $this->channel->queue_declare(self::QUEUE, false, false, false, false);
    }

    /**
     * @throws JsonException
     */
    public function send(EmailTemplate $emailTemplate): void
    {
        $emailTemplateJson = json_encode($emailTemplate->toArray(), JSON_THROW_ON_ERROR);
        $message = new AMQPMessage($emailTemplateJson);
        $this->channel->basic_publish($message, '', self::QUEUE);
    }
}
