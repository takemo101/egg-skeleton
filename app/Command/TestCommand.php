<?php

namespace App\Command;

use App\Entity\Blog;
use Cycle\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Egg\Console\Command\EggCommand;

final class TestCommand extends EggCommand
{
    public const Name = 'test';

    public const Description = 'test';

    protected function configure(): void
    {
        $this
            ->setName(self::Name)
            ->setDescription(self::Description);
    }

    /**
     * コマンド実行
     *
     * @param EntityManagerInterface $manager
     * @return integer
     */
    public function handle(EntityManagerInterface $manager): int
    {
        $blog = new Blog(
            id: uniqid(),
            title: 'test',
        );

        $manager
            ->persist($blog)
            ->run();

        return self::SUCCESS;
    }
}
