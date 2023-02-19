<?php

namespace Module\Database;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Takemo101\Egg\Module\Module;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Cycle\ORM\ORM;
use Cycle\ORM\Factory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Module\Database\Command\InitCommand;
use Module\Database\Command\MakeMigrationCommand;
use Module\Database\Command\MigrateCommand;
use Module\Database\Command\RollbackCommand;
use Takemo101\Egg\Console\Commands;

final class DatabaseModule extends Module
{
    /**
     * モジュールを起動する
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->container->singleton(
            DatabaseProviderInterface::class,
            fn () => new DatabaseManager(
                new DatabaseConfig(config('cycle.database', []))
            )
        )
            ->alias(
                DatabaseProviderInterface::class,
                DatabaseManager::class
            );

        $this->app->container->bind(
            DatabaseInterface::class,
            fn () => $this->app->container
                ->make(DatabaseProviderInterface::class)
                ->database(),
        );

        $this->app->container->singleton(
            Tokenizer::class,
            fn () => new Tokenizer(
                new TokenizerConfig(config('cycle.orm.tokenizer', [])),
            )
        );

        $this->app->container->singleton(
            ClassesInterface::class,
            fn () => $this->app->container->make(Tokenizer::class)
                ->classLocator(),
        )
            ->alias(
                ClassesInterface::class,
                ClassLocator::class,
            );

        $this->app->container->singleton(
            Schema::class,
            fn () => new Schema(
                (new Compiler())->compile(
                    $this->app->container
                        ->make(Registry::class),
                    [
                        // Annotated
                        new Embeddings($this->app->container->make(ClassesInterface::class)),
                        new Entities($this->app->container->make(ClassesInterface::class)),
                        new TableInheritance(),
                        new MergeColumns(),
                        new MergeIndexes(),
                        // Generator
                        new ResetTables(),
                        new GenerateRelations(),
                        new GenerateModifiers(),
                        new ValidateEntities(),
                        new RenderTables(),
                        new RenderRelations(),
                        new RenderModifiers(),
                        new SyncTables(),
                        new GenerateTypecast(),
                    ],
                ),
            ),
        );

        $this->app->container->singleton(
            ORMInterface::class,
            function () {
                /** @var DatabaseProviderInterface */
                $dbal = $this->app->container->make(DatabaseProviderInterface::class);
                return new ORM(
                    new Factory($dbal),
                    $this->app->container->make(Schema::class),
                );
            },
        )
            ->alias(
                ORMInterface::class,
                ORM::class
            );

        $this->app->container->singleton(
            EntityManagerInterface::class,
            fn () => new EntityManager(
                $this->app->container->make(ORMInterface::class),
            ),
        )
            ->alias(
                EntityManagerInterface::class,
                EntityManager::class,
            );

        $this->app->container->singleton(
            Migrator::class,
            function () {
                /** @var DatabaseManager */
                $dbal = $this->app->container->make(DatabaseProviderInterface::class);

                $config = new MigrationConfig(config('cycle.migrations', []));

                $repository = new FileRepository($config);

                return new Migrator($config, $dbal, $repository);
            },
        );

        $this->hook()->register(
            Commands::class,
            fn (Commands $commands) => $commands->add(
                InitCommand::class,
                MigrateCommand::class,
                RollbackCommand::class,
                MakeMigrationCommand::class,
            )
        );
    }
}
