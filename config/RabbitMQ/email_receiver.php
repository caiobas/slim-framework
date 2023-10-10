<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connectionData = $container->get('settings')['rabbitmq']['connection'];
$swiftMailer = $container->get(Swift_Mailer::class);

$connection = new AMQPStreamConnection(
    $connectionData['host'],
    $connectionData['port'],
    $connectionData['username'],
    $connectionData['password'],
);
$channel = $connection->channel();
$channel->queue_declare('email_sender', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) use ($swiftMailer) {
    $jsonMsg = json_decode($msg->body, false, 512, JSON_THROW_ON_ERROR);
    echo '  Received email message: from ', $jsonMsg->from->email, ' to ', $jsonMsg->emailTo,  "\n";

    $swiftMessage = (new Swift_Message($jsonMsg->subject))
        ->setFrom([$jsonMsg->from->email => $jsonMsg->from->name])
        ->setTo([$jsonMsg->emailTo])
        ->setBody($jsonMsg->body);

    $swiftMailer->send($swiftMessage);
    echo '  Email sent.',  "\n";
    echo ' [x] End message.',  "\n";
};

$channel->basic_consume('email_sender', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
