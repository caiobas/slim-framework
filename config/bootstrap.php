<?php

// bootstrap.php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$ENV = $_ENV['ENV'] ?? 'dev';

$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__ . '/../config/settings.php';
$containerBuilder->addDefinitions($settings);

// Import services
$dependencies = require __DIR__ . '/../config/services.php';
$containerBuilder->addDefinitions($dependencies);

// Initialize app with PHP-DI
$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();
