<?php

use App\Support\Latte\LatteFileLoader;
use App\Support\Path\AppPath;
use Takemo101\Egg\Support\Injector\ContainerContract;
use Latte\Engine as Latte;
use Takemo101\Egg\Kernel\ApplicationPath;

return function (ContainerContract $c) {
    $singletons = [
        Latte::class => function (ContainerContract $c) {

            /** @var ApplicationPath */
            $applicationPath = $c->make(ApplicationPath::class);

            /** @var AppPath */
            $appPath = $c->make(AppPath::class);

            $latte = new Latte();

            $latte->setTempDirectory($applicationPath->storagePath(
                config('setting.latte-cache-path', 'cache/latte')
            ));
            $latte->setLoader(
                new LatteFileLoader(
                    $appPath->lattePath(),
                ),
            );

            return $latte;
        },
        AppPath::class => function (ContainerContract $c) {

            /** @var ApplicationPath */
            $appPath = $c->make(ApplicationPath::class);

            return new AppPath(
                resourcePath: config('setting.resource-path', 'resource'),
                lattePath: config('setting.latte-path', 'latte'),
                path: $appPath,
            );
        },
    ];

    foreach ($singletons as $abstract => $class) {
        $c->singleton($abstract, $class);
    }
};
