<?php

declare(strict_types=1);

namespace App\Infrastructure\CycleOrm\Table;

final class SessionTable
{
    public const TABLE_NAME = 'chat_sessions';

    public const UUID = 'uuid';
    public const ACCOUNT_UUID = 'account_uuid';
    public const AGENT_NAME = 'agent_name';
    public const TITLE = 'title';
    public const HISTORY = 'history';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const FINISHED_AT = 'finished_at';
}
