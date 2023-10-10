<?php

declare(strict_types=1);

use App\Controller\HistoryController;
use App\Controller\SignInController;
use App\Controller\SignUpController;
use App\Controller\StockController;
use Slim\App;

return static function (App $app) {
    // unprotected routes
    $app->post('/sign-up', SignUpController::class);
    $app->post('/sign-in', SignInController::class);

    // protected routes
    $app->get('/stock', StockController::class);
    $app->get('/history', HistoryController::class);
};
