<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Utilities;

use PublishingKit\Utilities\Collections\Collection;
use PublishingKit\Utilities\Collections\LazyCollection;
use Tests\SimpleTestCase;
use ReflectionClass;
use Mockery as m;
use JsonSerializable;
use ArrayIterator;
use IteratorAggregate;

final class LazyCollectionTest extends SimpleTestCase
{
    /**
     * @var LazyCollection
     */
    private $collection;

    protected function setUp(): void
    {
        $items = [];
        $this->collection = new LazyCollection(function () {
            for ($i = 0; $i < 5; $i++) {
                yield $i;
            }
        });
    }

    protected function tearDown(): void
    {
        $this->collection = null;
        $reflect = new ReflectionClass(LazyCollection::class);
        $macros = $reflect->getProperty('macros');
        $macros->setAccessible(true);
        $macros->setValue(null);
    }

    public function testCanBeCreated()
    {
        $collection = new LazyCollection(function () {
            for ($i = 0; $i < 5; $i++) {
                yield $i;
            }
        });
        $this->assertInstanceOf(LazyCollection::class, $collection);
    }

    public function testCanBeCreatedWithNullSource()
    {
        $collection = new LazyCollection();
        $this->assertInstanceOf(LazyCollection::class, $collection);
    }

    public function testCanBeCreatedFromCollectable()
    {
        $collection = new LazyCollection(new Collection([1, 2, 3]));
        $this->assertInstanceOf(LazyCollection::class, $collection);
    }

    public function testCanBeCreatedFromJsonSerializable()
    {
        $source = new class ([1, 2, 3]) implements JsonSerializable {
            public $items;

            public function __construct(array $items)
            {
                $this->items = $items;
            }

            public function jsonSerialize()
            {
                return $this->items;
            }
        };
        $collection = new LazyCollection($source);
        $this->assertInstanceOf(LazyCollection::class, $collection);
    }

    public function testCanBeCreatedFromTraversable()
    {
        $source = new class ([1, 2, 3]) implements IteratorAggregate {
            public $items;

            public function __construct(array $items)
            {
                $this->items = $items;
            }

            public function getIterator()
            {
                return new ArrayIterator($this->items);
            }
        };
        $collection = new LazyCollection($source);
        $this->assertInstanceOf(LazyCollection::class, $collection);
    }

    public function testCanBeCalledStatically()
    {
        $this->collection = LazyCollection::make(function () {
            for ($i = 0; $i < 5; $i++) {
                yield $i;
            }
        });
        $this->assertInstanceOf(LazyCollection::class, $this->collection);
    }

    public function testImplementsCountable()
    {
        $this->assertInstanceOf('Countable', $this->collection);
    }

    public function testCanCountCorrectly()
    {
        $this->assertSame(5, $this->collection->count());
    }

    public function testImplementsIteratorAggregate()
    {
        $this->assertInstanceOf('IteratorAggregate', $this->collection);
        $this->assertInstanceOf('Generator', $this->collection->getIterator());
    }

    public function testImplementsJsonSerializable()
    {
        $this->assertInstanceOf('JsonSerializable', $this->collection);
    }

    public function testCanJsonSerialize()
    {
        $this->assertSame(json_encode([0, 1, 2, 3, 4]), $this->collection->jsonSerialize());
    }

    public function testCanConvertToJson()
    {
        $this->assertSame(json_encode([0, 1, 2, 3, 4]), $this->collection->toJson());
    }

    public function testCanConvertToArray()
    {
        $this->assertSame([0, 1, 2, 3, 4], $this->collection->toArray());
    }

    public function testImplementsMap()
    {
        $this->assertSame([0, 1, 8, 27, 64], $this->collection->map(function ($item) {
            return ($item * $item * $item);
        })->toArray());
    }

    public function testImplementsFilter()
    {
        $this->assertSame([0, 1, 2], $this->collection->filter(function ($item) {
            return $item < 3;
        })->toArray());
    }

    public function testFilterByValue()
    {
        $this->assertSame([1 => 1, 2 => 2, 3 => 3, 4 => 4], $this->collection->filter()->all());
    }

    public function testImplementsReject()
    {
        $this->assertSame([0, 1, 2], $this->collection->reject(function ($item) {
            return $item >= 3;
        })->toArray());
    }

    public function testImplementsReduce()
    {
        $this->assertSame(10, $this->collection->reduce(function ($total, $item) {
            return $total += $item;
        }));
    }

    public function testAll()
    {
        $this->assertSame([0, 1, 2, 3, 4], $this->collection->all());
    }

    public function testAllArray()
    {
        $collection = new LazyCollection([0, 1, 2]);
        $this->assertSame([0, 1, 2], $collection->all());
    }

    public function testImplementsPluck()
    {
        $items = [[
            'foo' => 1,
            'bar' => 2
        ], [
            'foo' => 3,
            'bar' => 4
        ], [
            'foo' => 5,
            'bar' => 6
        ]];
        $this->collection = new LazyCollection($items);
        $this->assertSame([1, 3, 5], $this->collection->pluck('foo')->toArray());
    }

    public function testImplementsEach()
    {
        /** @var DateTime|\PHPUnit\Framework\MockObject\MockObject $date */
        $date = m::mock('DateTime');
        $date->shouldReceive('setTimezone')
            ->with('Europe/London')
            ->once()
            ->andReturn(null);
        $this->collection = new LazyCollection([$date]);
        $this->collection->each(function ($item) {
            $item->setTimezone('Europe/London');
        });
    }

    public function testImplementsSerializable()
    {
        $this->assertInstanceOf('Serializable', $this->collection);
    }

    public function testSerializeAndUnserialize()
    {
        $items = [1, 2, 3, 4, 5];
        $collection = new LazyCollection($items);
        $data = $collection->serialize();
        $this->assertEquals(serialize($items), $data);
        $newCollection = new LazyCollection([]);
        $newCollection->unserialize($data);
        $this->assertEquals($collection, $newCollection);
    }

    public function testDebug()
    {
        $this->assertSame([0, 1, 2, 3, 4], $this->collection->__debugInfo());
    }

    public function testCastingObjectsToArray()
    {
        $foo = new class {
            public $a = 1;
            public $b = 2;
        };
        $collection = new LazyCollection($foo);
        $this->assertEquals(['a' => 1, 'b' => 2], $collection->all());
    }
}
