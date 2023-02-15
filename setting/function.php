<?php

use Symfony\Component\HttpFoundation\Response;
use Takemo101\Egg\Kernel\Application;
use Takemo101\Egg\Routing\RouteBuilder;
use Takemo101\Egg\Support\Hook\Hook;
use Takemo101\Egg\Support\StaticContainer;

/** @var Hook */
$hook = StaticContainer::get('hook');
/** @var Application */
$app = StaticContainer::get('app');

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
