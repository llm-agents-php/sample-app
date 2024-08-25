<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Entity;

use App\Application\Entity\Json;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    public function testFromString(): void
    {
        $jsonString = '{"key":"value"}';
        $json = Json::fromString($jsonString);

        $this->assertInstanceOf(Json::class, $json);
        $this->assertEquals(['key' => 'value'], $json->data);
    }

    public function testConstructor(): void
    {
        $data = ['foo' => 'bar'];
        $json = new Json($data);

        $this->assertEquals($data, $json->data);
    }

    public function testTypecastWithEmptyValue(): void
    {
        $json = Json::typecast('');

        $this->assertInstanceOf(Json::class, $json);
        $this->assertEquals([], $json->data);
    }

    public function testTypecastWithValidJson(): void
    {
        $jsonString = '{"name":"John","age":30}';
        $json = Json::typecast($jsonString);

        $this->assertInstanceOf(Json::class, $json);
        $this->assertEquals(['name' => 'John', 'age' => 30], $json->data);
    }

    public function testTypecastWithInvalidJson(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Json::typecast('{invalid json}');
    }

    public function testJsonSerializeWithArray(): void
    {
        $data = ['a' => 1, 'b' => 2];
        $json = new Json($data);

        $this->assertEquals($data, $json->jsonSerialize());
    }

    public function testJsonSerializeWithJsonSerializable(): void
    {
        $jsonSerializable = new class implements \JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['x' => 'y'];
            }
        };

        $json = new Json($jsonSerializable);

        $this->assertEquals(['x' => 'y'], $json->jsonSerialize());
    }

    public function testToString(): void
    {
        $data = ['hello' => 'world'];
        $json = new Json($data);

        $this->assertEquals('{"hello":"world"}', (string) $json);
    }
}
