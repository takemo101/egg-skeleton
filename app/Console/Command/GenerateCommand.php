<?php

namespace App\Console\Command;

use App\Module\View\Path\ResourcePath;
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
        ResourcePath $resourcePath,
        Latte $latte,
    ) {
        $lattePath = $resourcePath->lattePath();

        $files = $this->glob($fs, $lattePath . '/page/*');

        $fs->deleteDirectory(
            $resourcePath->resourcePath('generate'),
        );

        foreach ($files as $file) {
            $file = str_replace($lattePath, '', $file);
            $render = $latte->renderToString($file);

            $generateFile = str_replace('.latte.html', '.html', $file);
            $generateFile = str_replace('page/', '', $generateFile);
            $generateFile = $resourcePath->resourcePath("generate/{$generateFile}");

            $generateDir = dirname($generateFile);

            if (!$fs->exists($generateDir)) {
                $fs->makeDirectory($generateDir);
            }

            $fs->write(
                $generateFile,
                $render,
            );
        }

        $htaccessFile = $resourcePath->resourcePath("htaccess/.htaccess");

        $fs->copy($htaccessFile, $resourcePath->resourcePath('generate/.htaccess'));

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
