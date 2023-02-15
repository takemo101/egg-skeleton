<?php

namespace App\Module\View;

use App\Module\View\ErrorHandler\HttpErrorHandler;
use App\Module\View\Latte\LatteFileLoader;
use App\Module\View\Path\ResourcePath;
use App\Module\View\Session\FlashErrorMessages;
use App\Module\View\Session\FlashOldInputs;
use Takemo101\Egg\Module\Module;
use Takemo101\Egg\Support\Injector\ContainerContract;
use Latte\Engine as Latte;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Takemo101\Egg\Http\HttpErrorHandlerContract;
use Takemo101\Egg\Kernel\ApplicationEnvironment;
use Takemo101\Egg\Kernel\ApplicationPath;
use Takemo101\Egg\Support\Log\Loggers;

final class ViewModule extends Module
{
    /**
     * モジュールを起動する
     *
     * @return void
     */
    public function boot(): void
    {
        // テンプレートエンジン
        $singletons = [
            Latte::class => function (ContainerContract $c) {

                /** @var ApplicationPath */
                $applicationPath = $c->make(ApplicationPath::class);

                /** @var ResourcePath */
                $resourcePath = $c->make(ResourcePath::class);

                $latte = new Latte();

                $latte->setTempDirectory($applicationPath->storagePath(
                    config('setting.latte-cache-path', 'cache/latte')
                ));
                $latte->setLoader(
                    new LatteFileLoader(
                        $resourcePath->lattePath(),
                    ),
                );

                return $latte;
            },

            // リソースパス
            ResourcePath::class => function (ContainerContract $c) {

                /** @var ApplicationPath */
                $appPath = $c->make(ApplicationPath::class);

                return new ResourcePath(
                    resourcePath: $appPath->basePath(
                        config('setting.resource-path', 'resource'),
                    ),
                    lattePath: $appPath->basePath(
                        config('setting.latte-path', 'resource/latte'),
                    ),
                );
            },

            // Validator
            ValidatorInterface::class => function () {
                return Validation::createValidatorBuilder()
                    ->enableAnnotationMapping()
                    ->getValidator();
            },
        ];

        foreach ($singletons as $abstract => $class) {
            $this->app->container->singleton($abstract, $class);
        }

        $this->hook()
            ->register(
                HttpErrorHandlerContract::class,
                fn () => new HttpErrorHandler(
                    $this->app->container->make(ApplicationEnvironment::class),
                    $this->app->container->make(Loggers::class),
                    $this->app->container,
                ),
            )
            ->register(
                Session::class,
                function (Session $session) {
                    /** @var Request */
                    $request = $this->app->container->make(Request::class);

                    $inputs = new FlashOldInputs(
                        $session->getFlashBag(),
                    );

                    $inputs->put($request->request->all());

                    $this->app->container->instance(
                        FlashOldInputs::class,
                        $inputs,
                    );

                    $this->app->container->instance(
                        FlashErrorMessages::class,
                        new FlashErrorMessages(
                            $session->getFlashBag(),
                        ),
                    );

                    return $session;
                },
            );
    }
}
