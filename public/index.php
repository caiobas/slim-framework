<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

// Setup Basic Auth
$auth = require __DIR__ . '/../app/auth.php';
$auth($app);

$displayErrorDetails = $ENV == 'dev';
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

// Error Handler
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

$app->run();
