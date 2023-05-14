<?php

use Module\Latte\Latte\LatteViewGenerator;
use Module\Latte\Session\ErrorMessages;
use Module\Latte\Session\OldInputs;
use Takemo101\Egg\Kernel\Application;
use Takemo101\Egg\Support\ServiceAccessor\AppAccessor as App;

if (!function_exists('latte')) {
    /**
     * Latteでテンプレートをレンダリングしてレスポンスを返す
     *
     * @param string $path
     * @param object|mixed[] $params
     * @param string|null $block
     * @return string
     */
    function latte(
        string $path,
        object|array $params = [],
        ?string $block = null,
    ): string {
        /** @var LatteViewGenerator */
        $latte = App::container()->make(LatteViewGenerator::class);

        return $latte->generate($path, $params, $block);
    }
}

if (!function_exists('old')) {
    /**
     * 前の入力値
     *
     * @return mixed
     */
    function old(?string $key = null, mixed $default = null)
    {
        /** @var OldInputs */
        $inputs = App::container()->make(OldInputs::class);

        return $key
            ? $inputs->get($key, $default)
            : $inputs;
    }
}

if (!function_exists('errors')) {
    /**
     * 前の入力値
     *
     * @return mixed
     */
    function errors(?string $key = null)
    {
        /** @var ErrorMessages */
        $errors = App::container()->make(ErrorMessages::class);

        return $key
            ? $errors->first($key)
            : $errors;
    }
}
