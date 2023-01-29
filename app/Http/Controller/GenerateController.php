<?php

namespace App\Http\Controller;

use App\Support\Path\AppPath;
use Latte\Engine as Latte;
use Takemo101\Egg\Support\Filesystem\LocalSystem;

class GenerateController
{
    public function __construct(
        private readonly LocalSystem $fs,
        private readonly AppPath $appPath,
    ) {
        //
    }

    public function generate(
        Latte $latte,
    ) {
        $lattePath = $this->appPath->lattePath();

        $files = $this->glob($lattePath . '/page/*');

        foreach ($files as $file) {
            $file = str_replace($lattePath, '', $file);
            $render = $latte->renderToString($file);

            $generateFile = str_replace('.latte.html', '.html', $file);
            $generateFile = str_replace('page/', '', $generateFile);
            $generateFile = $this->appPath->resourcePath("generate/{$generateFile}");

            $generateDir = dirname($generateFile);

            if (!$this->fs->exists($generateDir)) {
                $this->fs->makeDirectory($generateDir);
            }

            $this->fs->write(
                $generateFile,
                $render,
            );
        }

        $htaccessFile = $this->appPath->resourcePath("htaccess/.htaccess");

        $this->fs->copy($htaccessFile, $this->appPath->resourcePath('generate/.htaccess'));

        return latte("generate.latte.html", compact('files'));
    }

    private function glob(string $path): array
    {
        $files = $this->fs->glob($path);

        $result = [];

        foreach ($files as $file) {
            if ($this->fs->isDirectory($file)) {
                $result = [
                    ...$result,
                    ...$this->glob($file . '/*'),
                ];
            } else {
                $result[] = $file;
            }
        }

        return $result;
    }
}
