<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Kekos\DoctrineConsoleFactory\MigrationsConfigurationLoader;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader as DoctrineConfigurationLoader;
use App\Service\StockService\StooqHttpClient;
use PhpAmqpLib\Connection\AMQPStreamConnection;

return [
        Swift_Mailer::class => function(ContainerInterface $c) {
            $connection = $c->get('settings')['swift_mailer'];

            $host = $connection['host'];
            $port = $connection['port'];
            $username = $connection['username'];
            $password = $connection['password'];

            $transport = (new Swift_SmtpTransport($host, $port))
                ->setUsername($username)
                ->setPassword($password)
            ;

            return new Swift_Mailer($transport);
        },
        EntityManager::class => function (ContainerInterface $c): EntityManager {
            /** @var array $settings */
            $settings = $c->get('settings');

            $cache = $settings['doctrine']['dev_mode'] ?
                DoctrineProvider::wrap(new ArrayAdapter()) :
                DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']));

            $config = Setup::createAttributeMetadataConfiguration(
                $settings['doctrine']['metadata_dirs'],
                $settings['doctrine']['dev_mode'],
                null,
                $cache
            );

            return EntityManager::create($settings['doctrine']['connection'], $config);
        },
        DoctrineConfigurationLoader::class => function (ContainerInterface $c) {
            $settings = $c->get('settings')['doctrine']['migrations'];

            return new MigrationsConfigurationLoader($settings);
        },
        StooqHttpClient::class => function(ContainerInterface $c) {
            $url = $c->get('settings')['stooq_url'];
            return new StooqHttpClient($url);
        },
        AMQPStreamConnection::class => function(ContainerInterface $c) {
            $connection = $c->get('settings')['rabbitmq']['connection'];

            return new AMQPStreamConnection(
                $connection['host'],
                $connection['port'],
                $connection['username'],
                $connection['password'],
            );
        }
    ];
