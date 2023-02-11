<?php

use App\Console\Command\GenerateCommand;
use Takemo101\Egg\Console\Command\VersionCommand;
use Takemo101\Egg\Console\Commands;

return function (Commands $commands) {
    $commands->add(
        VersionCommand::class,
        GenerateCommand::class,
    );
};
