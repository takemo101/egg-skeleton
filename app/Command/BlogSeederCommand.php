<?php

namespace App\Command;

use App\Entity\Blog;
use App\Entity\Category;
use Carbon\Carbon;
use Cycle\ORM\EntityManagerInterface;
use Microcms\Client;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Egg\Console\Command\EggCommand;

final class BlogSeederCommand extends EggCommand
{
    public const Name = 'seed:blog';

    public const Description = 'blog seeding';

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
     * @param Client $client
     * @return integer
     */
    public function handle(EntityManagerInterface $manager, Client $client): int
    {

        $contents = $client->list('categories');

        /** @var array<string,Category> */
        $categories = [];

        foreach ($contents->contents as $content) {
            $category = new Category(
                id: $content->id,
                name: $content->name,
                createdAt: new Carbon($content->createdAt),
                updatedAt: new Carbon($content->updatedAt),
            );

            $manager
                ->persist($category)
                ->run();

            $categories[$category->id] = $category;
        }

        $contents = $client->list('blogs');

        foreach ($contents->contents as $content) {
            $blog = new Blog(
                id: $content->id,
                eyecatch: isset($content->eyecatch)
                    ? $content->eyecatch->url
                    : null,
                title: $content->title,
                content: $content->content,
                createdAt: new Carbon($content->createdAt),
                updatedAt: new Carbon($content->updatedAt),
                publishedAt: new Carbon($content->publishedAt),
                category: $content->category
                    ? $categories[$content->category->id]
                    : null,
            );

            $manager
                ->persist($blog)
                ->run();
        }

        return self::SUCCESS;
    }
}
