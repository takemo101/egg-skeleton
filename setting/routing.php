<?php

use App\Http\Controller\GenerateController;
use App\Http\Controller\PageController;
use Takemo101\Egg\Routing\RouteBuilder;

return function (RouteBuilder $r) {
    $r->get('/api', [PageController::class, 'api'])
        ->name('api');

    $r->get('/[:path]', [PageController::class, 'page'])
        ->name('page.index');
};
