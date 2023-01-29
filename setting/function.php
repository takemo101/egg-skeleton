<?php

use Symfony\Component\HttpFoundation\Response;
use Takemo101\Egg\Routing\RouteBuilder;
use Takemo101\Egg\Support\Hook\Hook;
use Takemo101\Egg\Support\StaticContainer;

// ヘルパの読み込み
require __DIR__ . '/helper.php';

/** @var Hook */
$hook = StaticContainer::get('hook');

$hook->register(
    RouteBuilder::class,
    function (RouteBuilder $r) {
        $r->get('/phpinfo', function (Response $response) {
            phpinfo();
        })
            ->name('phpinfo');

        return $r;
    },
);
