<?php

use Takemo101\Egg\Support\Injector\ContainerContract;

return function (ContainerContract $c) {
    $singletons = [
        //
    ];

    foreach ($singletons as $abstract => $class) {
        $c->singleton($abstract, $class);
    }
};
