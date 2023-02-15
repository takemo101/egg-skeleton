<?php

use Takemo101\Egg\Kernel\Application;
use Takemo101\Egg\Support\StaticContainer;
use Takemo101\Egg\Http\Filter\CsrfFilter;

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
