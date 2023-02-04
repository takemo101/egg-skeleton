<?php

namespace App\Console\Command;

use App\Support\Path\AppPath;
use Symfony\Component\Console\Output\OutputInterface;
use Latte\Engine as Latte;
use Takemo101\Egg\Console\Command\EggCommand;
use Takemo101\Egg\Support\Filesystem\LocalSystem;

final class GenerateCommand extends EggCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('generate');
    }

    public function handle(
        OutputInterface $output,
        LocalSystem $fs,
        AppPath $appPath,
        Latte $latte,
    ) {
        $lattePath = $appPath->lattePath();

        $files = $this->glob($fs, $lattePath . '/page/*');

        $fs->deleteDirectory(
            $appPath->resourcePath('generate'),
        );

        foreach ($files as $file) {
            $file = str_replace($lattePath, '', $file);
            $render = $latte->renderToString($file);

            $generateFile = str_replace('.latte.html', '.html', $file);
            $generateFile = str_replace('page/', '', $generateFile);
            $generateFile = $appPath->resourcePath("generate/{$generateFile}");

            $generateDir = dirname($generateFile);

            if (!$fs->exists($generateDir)) {
                $fs->makeDirectory($generateDir);
            }

            $fs->write(
                $generateFile,
                $render,
            );
        }

        $htaccessFile = $appPath->resourcePath("htaccess/.htaccess");

        $fs->copy($htaccessFile, $appPath->resourcePath('generate/.htaccess'));

        $output->writeln('generate!');

        return self::SUCCESS;
    }

    /**
     * ファイルパスを全て取得
     *
     * @param string $path
     * @return array
     */
    private function glob(LocalSystem $fs, string $path): array
    {
        $files = $fs->glob($path);

        $result = [];

        foreach ($files as $file) {
            if ($fs->isDirectory($file)) {
                $result = [
                    ...$result,
                    ...$this->glob($fs, $file . '/*'),
                ];
            } else {
                $result[] = $file;
            }
        }

        return $result;
    }
}
