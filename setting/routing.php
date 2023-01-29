<?php

use App\Http\Controller\GenerateController;
use App\Http\Controller\PageController;
use Takemo101\Egg\Routing\RouteBuilder;

return function (RouteBuilder $r) {

    $r->get('/generate', [GenerateController::class, 'generate'])
        ->name('generate');

    $r->get('/', [PageController::class, 'page'])
        ->name('page.home');
    $r->get('/[*:path]', [PageController::class, 'page'])
        ->name('page.index');
};
