<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Entity;

use App\Application\Entity\Uuid;
use Tests\TestCase;

final class UuidTest extends TestCase
{
    public function testGenerate(): void
    {
        $uuid = Uuid::generate();
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid((string) $uuid));
    }

    public function testFromString(): void
    {
        $uuidString = 'f81d4fae-7dec-11d0-a765-00a0c91e6bf6';
        $uuid = Uuid::fromString($uuidString);
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals($uuidString, (string) $uuid);
    }

    public function testTypecast(): void
    {
        $uuidString = 'f81d4fae-7dec-11d0-a765-00a0c91e6bf6';
        $uuid = Uuid::typecast($uuidString);
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals($uuidString, (string) $uuid);
    }

    public function testEquals(): void
    {
        $uuid1 = Uuid::generate();
        $uuid2 = Uuid::fromString((string) $uuid1);
        $uuid3 = Uuid::generate();

        $this->assertTrue($uuid1->equals($uuid2));
        $this->assertTrue($uuid2->equals($uuid1));
        $this->assertFalse($uuid1->equals($uuid3));
    }

    public function testToString(): void
    {
        $uuid = Uuid::generate();
        $this->assertIsString((string) $uuid);
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid((string) $uuid));
    }

    public function testJsonSerialize(): void
    {
        $uuid = Uuid::generate();
        $json = \json_encode($uuid);
        $this->assertIsString($json);
        $this->assertTrue(\Ramsey\Uuid\Uuid::isValid(\json_decode($json)));
    }

    public function testInvalidUuidString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Uuid::fromString('invalid-uuid');
    }
}
