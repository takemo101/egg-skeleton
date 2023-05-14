<?php

namespace Module\Latte;

use Module\Latte\ErrorHandler\HttpErrorHandler;
use Module\Latte\Latte\LatteFileLoader;
use Module\Latte\Path\ResourcePath;
use Module\Latte\Session\ErrorMessages;
use Module\Latte\Session\OldInputs;
use Takemo101\Egg\Module\Module;
use Takemo101\Egg\Support\Injector\ContainerContract;
use Latte\Engine as Latte;
use Module\Latte\Command\ViewClearCommand;
use Module\Latte\Latte\LatteViewGenerator;
use Module\Latte\Support\ViewDataFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Takemo101\Egg\Console\Commands;
use Takemo101\Egg\Http\HttpErrorHandlerContract;
use Takemo101\Egg\Kernel\ApplicationEnvironment;
use Takemo101\Egg\Kernel\ApplicationPath;
use Takemo101\Egg\Support\Log\Loggers;

final class LatteModule extends Module
{
    /**
     * モジュールを起動する
     *
     * @return void
     */
    public function boot(): void
    {
        // ヘルパー関数の読み込み
        require __DIR__ . '/helper.php';

        $this->registerView();
        $this->registerViewHook();

        $this->mergeConfig(
            'latte',
            __DIR__ . '/../config/config.php',
        );

        $this->publishes('latte', [
            __DIR__ . '/../config/config.php' => $this->app
                ->path()
                ->getConfigPath('latte.php'),
        ]);
    }

    /**
     * ビュー関連のサービスを登録する
     *
     * @return void
     */
    private function registerView(): void
    {
        foreach ([
            // テンプレートエンジン
            Latte::class => function (ContainerContract $c) {

                /** @var ApplicationPath */
                $appPath = $c->make(ApplicationPath::class);

                /** @var ResourcePath */
                $resourcePath = $c->make(ResourcePath::class);

                $latte = new Latte();

                /** @var string */
                $cachePath = config('latte.path.cache', 'cache/latte');

                $latte->setTempDirectory($appPath->getStoragePath(
                    $cachePath,
                ));
                $latte->setLoader(
                    new LatteFileLoader(
                        $resourcePath->lattePath(),
                    ),
                );

                return $latte;
            },

            // 共有データ
            ViewDataFactory::class => fn (ContainerContract $c) => new ViewDataFactory($c),

            // テンプレート出力
            LatteViewGenerator::class => function (ContainerContract $c) {

                /** @var Latte */
                $latte = $c->make(Latte::class);

                $generator = new LatteViewGenerator(
                    $latte,
                );

                $generator->share('share', $c->make(ViewDataFactory::class));

                return $generator;
            },

            // リソースパス
            ResourcePath::class => function (ContainerContract $c) {

                /** @var ApplicationPath */
                $appPath = $c->make(ApplicationPath::class);

                /** @var string */
                $resourcePath = config('latte.path.resource', 'resource');

                /** @var string */
                $lattePath = config('latte.path.view', 'resource/latte');

                return new ResourcePath(
                    resourcePath: $appPath->getBasePath(
                        $resourcePath,
                    ),
                    lattePath: $appPath->getBasePath(
                        $lattePath,
                    ),
                );
            },

            // Validator
            ValidatorInterface::class => function () {
                return Validation::createValidatorBuilder()
                    ->enableAnnotationMapping()
                    ->getValidator();
            },
        ] as $abstract => $class) {
            $this->app->singleton($abstract, $class);
        }
    }

    /**
     * ビュー関連のフックを登録する
     *
     * @return void
     */
    private function registerViewHook(): void
    {
        $this->hook()
            // エラーハンドラーの入れ替え
            ->on(
                HttpErrorHandlerContract::class,
                fn () => $this->app->make(HttpErrorHandler::class),
            )
            // セッションからの入力値などの復元
            ->onByType(
                function (Session $session) {
                    /** @var Request */
                    $request = $this->app->make(Request::class);

                    $inputs = new OldInputs(
                        $session->getFlashBag(),
                    );

                    $inputs->put($request->request->all());

                    $this->app->instance(
                        OldInputs::class,
                        $inputs,
                    );

                    $this->app->instance(
                        ErrorMessages::class,
                        new ErrorMessages(
                            $session->getFlashBag(),
                        ),
                    );

                    return $session;
                },
            )
            // コマンド登録
            ->onByType(
                function (Commands $commands) {
                    return $commands->add(
                        ViewClearCommand::class,
                    );
                },
            );
    }
}
