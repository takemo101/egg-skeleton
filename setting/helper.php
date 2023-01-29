<?php

use Latte\Engine as Latte;
use Symfony\Component\HttpFoundation\Response;
use Takemo101\Egg\Kernel\Application;
use Takemo101\Egg\Support\StaticContainer;

if (!function_exists('latte')) {
    /**
     * Latteでテンプレートをレンダリングしてレスポンスを返す
     *
     * @param string $path
     * @param array $params
     * @param string|null $block
     * @return Response
     */
    function latte(string $path, object|array $params = [], ?string $block = null): Response
    {
        /** @var Application */
        $app = StaticContainer::get('app');

        /** @var Latte */
        $latte = $app->container->make(Latte::class);

        /** @var Response */
        $response = $app->container->make('response');

        return $response->setContent(
            $latte->renderToString($path, $params, $block),
        );
    }
}
