<?php

use Module\View\Support\ViewDataFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Takemo101\Egg\Routing\RouteBuilder;
use Takemo101\Egg\Support\ServiceAccessor\HookAccessor as Hook;

Hook::onByType(
    fn (Commands $commands) => $commands->add(
        VersionCommand::class,
    ),
);

Hook::onByType(
    fn (RootFilters $filters) => $filters->add(
        MethodOverrideFilter::class,
        SessionFilter::class,
        CsrfFilter::class,
    ),
);

Hook::onByType(
    function (RouteBuilder $r) {

        $r->addMatchTypes([
            'a' => '[a-z]',
        ]);

        $r->get('/phpinfo', function (Response $response) {
            phpinfo();
        })
            ->name('phpinfo');

        $r->get('/', function (Request $request, Response $response, CsrfFilter $csrf) {
            return $response->setContent('
            <form action="/" method="POST">
                <input type="hidden" name="' . CsrfFilter::TokenKey . '" value="' . $csrf->token() .  '">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="name" value="a">
                <input type="submit" value="put">
            </form>
        ');
        })
            ->name('home');

        $r->get('/test', function (Request $request, Response $response) {
            return 'test';
        })
            ->name('test');

        $r->put('/', function (Request $request, Response $response) {
            return $response->setContent('put-home');
        })
            ->name('home.edit');

        $r->get('/error', fn () => throw new NotFoundHttpException())
            ->name('error');

        $r->get('/log', function (Loggers $loggers) {
            $loggers->get('app')->info('test');
        })
            ->name('log');

        $r->group(function (RouteBuilder $r) {
            $r->get('/', function (Request $request, Response $response) {
                return new JsonResponse([
                    'a' => 'b',
                ]);
            })
                ->name('index');

            $r->get('/[a:id]', function (string $id) {
                echo $id;
            })
                ->name('show');

            $r->put('/[i:id]/edit', function (int $id) {
                echo $id;
            })
                ->name('edit');
        })
            ->path('group')
            ->name('group.');

        return $r;
    },
);

Hook::onByType(
    fn (Modules $modules) => $modules->add(
        HelperModule::class,
    ),
);

Hook::on(
    'after-response',
    function (Response $response) {
        return $response;
    },
);

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

// リクエストのフックによる強制https化
$hook->register(
    Request::class,
    function (Request $r) {
        if (config('setting.force_https', false)) {
            $r->server->set('HTTPS', 'on');
            $r->server->set('SSL', 'on');
            $r->server->set('HTTP_X_FORWARDED_PROTO', 'https');
            $r->server->set('HTTP_X_FORWARDED_PORT', '443');
            $r->server->set('SERVER_PORT', '443');
        }

        return $r;
    },
);

// Viewの共有データへのフック
$hook->register(
    ViewDataFactory::class,
    function (ViewDataFactory $factory) {
        $factory->addHandler('categories', function (ORMInterface $orm) {
            /** @var CategoryRepository */
            $repository = $orm->getRepository('category');

            return $repository->findAll();
        });

        return $factory;
    },
);
