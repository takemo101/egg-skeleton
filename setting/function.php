<?php

use App\Controller\ContactController;
use App\Controller\HomeController;
use Module\Latte\Support\ViewDataFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Takemo101\Egg\Console\Command\VersionCommand;
use Takemo101\Egg\Console\Commands;
use Takemo101\Egg\Http\Filter\CsrfFilter;
use Takemo101\Egg\Http\Filter\MethodOverrideFilter;
use Takemo101\Egg\Http\Filter\SessionFilter;
use Takemo101\Egg\Http\RootFilters;
use Takemo101\Egg\Module\HelperModule;
use Takemo101\Egg\Module\Modules;
use Takemo101\Egg\Routing\RouteBuilder;
use Takemo101\Egg\Support\ServiceAccessor\ContainerAccessor as Container;
use Takemo101\Egg\Support\ServiceAccessor\HookAccessor as Hook;
use Module\Latte\LatteModule;

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
    fn (Modules $modules) => $modules->add(
        HelperModule::class,
        LatteModule::class,
    ),
);

Hook::onByType(
    function (RouteBuilder $r) {

        $r->get('/', [HomeController::class, 'home'])->name('home');

        $r->post('contact', [ContactController::class, 'store'])->name('contact.store');

        $r->get('phpinfo', function (Response $response) {
            phpinfo();
        })
            ->name('phpinfo');

        return $r;
    },
);

// リクエストのフックによる強制https化
Hook::on(
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
Hook::onByType(
    fn (ViewDataFactory $factory) => $factory,
);
