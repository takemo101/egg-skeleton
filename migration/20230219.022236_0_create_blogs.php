<?php

use Cycle\Migrations\Migration;

final class Migration4f58fed415c212070b3ddefe8ff4b721 extends Migration
{
    public function up(): void
    {
        $this->table('blogs')
            ->addColumn('id', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 16,
            ])
            ->addColumn('title', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255,
            ])
            ->setPrimaryKeys(['id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('blogs')->drop();
    }
};
