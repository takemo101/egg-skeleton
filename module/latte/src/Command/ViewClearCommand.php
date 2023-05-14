<?php

namespace Module\Latte\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Egg\Console\Command\EggCommand;
use Takemo101\Egg\Kernel\ApplicationPath;
use Takemo101\Egg\Support\Filesystem\LocalSystemContract;

final class ViewClearCommand extends EggCommand
{
    public const Name = 'view:clear';

    public const Description = 'clear view cache';

    protected function configure(): void
    {
        $this
            ->setName(self::Name)
            ->setDescription(self::Description);
    }

    /**
     * コマンド実行
     *
     * @param OutputInterface $output
     * @param LocalSystemContract $fs
     * @param ApplicationPath $path
     * @return integer
     */
    public function handle(OutputInterface $output, LocalSystemContract $fs, ApplicationPath $path): int
    {
        /** @var string */
        $cachePath = config('latte.path.view', 'cache/latte');

        $pattern = $path->getStoragePath(
            $cachePath,
        );

        $paths = $fs->glob($pattern . '/*');

        // ファイルがない場合は終了
        if (empty($paths)) {
            $output->writeln('<info>nothing to clear</info>');

            return self::SUCCESS;
        }

        foreach ($paths as $path) {
            $fs->delete($path);
        }

        $output->writeln('<info>done!</info>');

        return self::SUCCESS;
    }
}
