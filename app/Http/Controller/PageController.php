<?php

namespace App\Http\Controller;

use App\Support\Path\AppPath;
use Takemo101\Egg\Http\Exception\NotFoundHttpException;
use Takemo101\Egg\Kernel\Application;
use Takemo101\Egg\Support\Filesystem\LocalSystem;

class PageController
{
    /**
     * テンプレートの階層構造をそのまま表示
     *
     * @param LocalSystem $fs
     * @param AppPath $appPath
     * @param string $path
     */
    public function page(
        LocalSystem $fs,
        AppPath $appPath,
        string $path,
    ) {
        $path = empty($path) ? 'index' : $path;

        $file = "page/{$path}.latte.html";

        if (!$fs->exists(
            $appPath->lattePath($file)
        )) {
            throw new NotFoundHttpException();
        }

        return latte($file);
    }

    /**
     * テンプレートの階層構造をそのまま表示
     *
     * @param LocalSystem $fs
     * @param AppPath $appPath
     * @param string $path
     */
    public function api()
    {
        return [
            'version' => Application::Version,
        ];
    }
}
