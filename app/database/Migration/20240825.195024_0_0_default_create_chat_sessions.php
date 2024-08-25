<?php

declare(strict_types=1);

namespace Database\Migration;

use Cycle\Migrations\Migration;

class OrmDefault4dbed8c48aa83c9c89d3b56438115aaf extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('chat_sessions')
        ->addColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => 'CURRENT_TIMESTAMP'])
        ->addColumn('updated_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('title', 'string', ['nullable' => true, 'defaultValue' => null, 'size' => 255])
        ->addColumn('history', 'json', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('finished_at', 'datetime', ['nullable' => true, 'defaultValue' => null])
        ->addColumn('uuid', 'uuid', ['nullable' => false, 'defaultValue' => null, 'size' => 36])
        ->addColumn('account_uuid', 'uuid', ['nullable' => false, 'defaultValue' => null, 'size' => 36])
        ->addColumn('agent_name', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255])
        ->setPrimaryKeys(['uuid'])
        ->create();
    }

    public function down(): void
    {
        $this->table('chat_sessions')->drop();
    }
}
