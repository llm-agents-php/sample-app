<?php

declare(strict_types=1);

namespace App\Domain\Chat;

use App\Application\Entity\Uuid;
use App\Infrastructure\CycleOrm\Repository\SessionRepository;
use App\Infrastructure\CycleOrm\Table\SessionTable;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior;
use LLM\Agents\Chat\SessionInterface;
use Ramsey\Uuid\UuidInterface;

#[Entity(
    role: Session::ROLE,
    repository: SessionRepository::class,
    table: SessionTable::TABLE_NAME
)]
#[Behavior\CreatedAt(field: 'createdAt', column: SessionTable::CREATED_AT)]
#[Behavior\UpdatedAt(field: 'updatedAt', column: SessionTable::UPDATED_AT)]
class Session implements SessionInterface
{
    public const ROLE = 'chat_session';

    public const F_UUID = 'uuid';
    public const F_ACCOUNT_UUID = 'accountUuid';
    public const F_AGENT_NAME = 'agentName';
    public const F_TITLE = 'title';
    public const F_HISTORY = 'history';
    public const F_CREATED_AT = 'createdAt';
    public const F_UPDATED_AT = 'updatedAt';
    public const F_FINISHED_AT = 'finishedAt';

    #[Column(type: 'string', name: SessionTable::TITLE, nullable: true, default: null)]
    public ?string $title = null;

    #[Column(type: 'json', name: SessionTable::HISTORY, typecast: History::class)]
    public History $history;

    public \DateTimeInterface $createdAt;
    public ?\DateTimeInterface $updatedAt = null;

    #[Column(type: 'datetime', name: SessionTable::FINISHED_AT, nullable: true, default: null)]
    public ?\DateTimeInterface $finishedAt = null;

    public function __construct(
        #[Column(type: 'uuid', name: SessionTable::UUID, primary: true, typecast: Uuid::class)]
        public Uuid $uuid,
        #[Column(type: 'uuid', name: SessionTable::ACCOUNT_UUID, typecast: Uuid::class)]
        public Uuid $accountUuid,
        #[Column(type: 'string', name: SessionTable::AGENT_NAME)]
        public string $agentName,
    ) {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;

        $this->updateHistory([]);
    }

    public function updateHistory(array $messages): void
    {
        $this->history = new History($messages);
    }

    public function isFinished(): bool
    {
        return $this->finishedAt !== null;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid->uuid;
    }

    public function getAgentName(): string
    {
        return $this->agentName;
    }
}
