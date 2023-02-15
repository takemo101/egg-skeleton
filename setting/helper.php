<?php

use App\Module\View\Session\FlashErrorMessages;
use Latte\Engine as Latte;
use Takemo101\Egg\Kernel\Application;
use Takemo101\Egg\Support\StaticContainer;
use App\Module\View\Session\FlashOldInputs;
use Takemo101\Egg\Http\Filter\CsrfFilter;

if (!function_exists('latte')) {
    /**
     * Latteでテンプレートをレンダリングしてレスポンスを返す
     *
     * @param string $path
     * @param object|mixed[] $params
     * @param string|null $block
     * @return string
     */
    function latte(string $path, object|array $params = [], ?string $block = null): string
    {
        /** @var Application */
        $app = StaticContainer::get('app');

        /** @var Latte */
        $latte = $app->container->make(Latte::class);

        return $latte->renderToString($path, $params, $block);
    }
}

if (!function_exists('old')) {
    /**
     * 前の入力値
     *
     * @return FlashOldInputs
     */
    function old(): FlashOldInputs
    {
        /** @var Application */
        $app = StaticContainer::get('app');

        /** @var FlashOldInputs */
        $inputs = $app->container->make(FlashOldInputs::class);

        return $inputs;
    }
}

if (!function_exists('errors')) {
    /**
     * 前の入力値
     *
     * @return FlashErrorMessages
     */
    function errors(): FlashErrorMessages
    {
        /** @var Application */
        $app = StaticContainer::get('app');

        /** @var FlashErrorMessages */
        $errors = $app->container->make(FlashErrorMessages::class);

        return $errors;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Csrfトークン取得
     *
     * @return string
     */
    function csrf_token(): string
    {
        /** @var Application */
        $app = StaticContainer::get('app');

        /** @var CsrfFilter */
        $filter = $app->container->make(CsrfFilter::class);

        return $filter->token();
    }
}
