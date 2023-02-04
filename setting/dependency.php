<?php

use App\Support\Latte\LatteFileLoader;
use App\Support\Path\AppPath;
use Takemo101\Egg\Support\Injector\ContainerContract;
use Latte\Engine as Latte;
use Takemo101\Egg\Kernel\ApplicationPath;

return function (ContainerContract $c) {
    $singletons = [
        // テンプレートエンジン
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

        // このアプリケーションのパス管理クラス
        AppPath::class => function (ContainerContract $c) {

            /** @var ApplicationPath */
            $appPath = $c->make(ApplicationPath::class);

            return new AppPath(
                resourcePath: $appPath->basePath(
                    config('setting.resource-path', 'resource'),
                ),
                lattePath: $appPath->basePath(
                    config('setting.latte-path', 'resource/latte'),
                ),
            );
        },
    ];

    foreach ($singletons as $abstract => $class) {
        $c->singleton($abstract, $class);
    }
};
