<?php

namespace Module\Database\Command;

use Cycle\Migrations\Migration;
use Cycle\Migrations\Migrator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Egg\Console\Command\EggCommand;

final class MakeMigrationCommand extends EggCommand
{
    public const Name = 'cycle:make:migration';

    public const Description = 'make a migration file';

    protected function configure(): void
    {
        $this
            ->setName(self::Name)
            ->setDescription(self::Description)
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'what do you do with the filename?',
            );
    }

    /**
     * コマンド実行
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Migrator $migrator
     * @return integer
     */
    public function handle(
        InputInterface $input,
        OutputInterface $output,
        Migrator $migrator,
    ): int {
        $migrator->configure();

        $filename = $input->getArgument('filename');

        $template = <<<EOT
<?php

use Cycle\Migrations\Migration;

final class %s extends Migration
{
    public function up(): void
    {
        \$this->table('xxx')
            ->addColumn('id', 'primary', [
                'nullable' => false,
                'default'  => null,
            ])
            ->addColumn('name', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255,
            ])
            ->setPrimaryKeys(['id'])
            ->create();
    }

    public function down(): void
    {
        \$this->table('xxx')->drop();
    }
};
EOT;

        $body = sprintf(
            $template,
            $this->generateMigrationClassName(),
        );

        $create = $migrator->getRepository()->registerMigration(
            $filename,
            Migration::class,
            $body,
        );

        $output->writeln('<info>Migration <comment>' . $create . '</comment> was successfully created!</info>');

        return self::SUCCESS;
    }

    /**
     * マイグレーションクラス名を生成する
     *
     * @return string
     */
    private function generateMigrationClassName(): string
    {
        return 'Migration' . bin2hex(random_bytes(16));
    }
}
