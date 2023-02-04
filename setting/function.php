<?php

use App\ErrorHandler\HttpErrorHandler;
use Symfony\Component\HttpFoundation\Response;
use Takemo101\Egg\Http\HttpErrorHandlerContract;
use Takemo101\Egg\Kernel\Application;
use Takemo101\Egg\Kernel\ApplicationEnvironment;
use Takemo101\Egg\Routing\RouteBuilder;
use Takemo101\Egg\Support\Hook\Hook;
use Takemo101\Egg\Support\Injector\ContainerContract;
use Takemo101\Egg\Support\Log\Loggers;
use Takemo101\Egg\Support\StaticContainer;

// ヘルパの読み込み
require __DIR__ . '/helper.php';

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

$hook->register(
    HttpErrorHandlerContract::class,
    fn () => new HttpErrorHandler(
        $app->container->make(ApplicationEnvironment::class),
        $app->container->make(Loggers::class),
    ),
);
