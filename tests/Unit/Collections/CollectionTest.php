<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\SimpleTestCase;
use PublishingKit\Utilities\Collections\Collection;
use Mockery as m;

final class CollectionTest extends SimpleTestCase
{
    /**
     * @var \PublishingKit\Collection\Collection
     */
    private $collection;

    protected function setUp(): void
    {
        $items = [];
        $this->collection = new Collection($items);
    }

    protected function tearDown(): void
    {
        $this->collection = null;
    }

    public function testCanBeCalledStatically()
    {
        $items = [
            'foo' => 'bar'
        ];
        $this->collection = Collection::make($items);
        $this->assertSame(1, $this->collection->count());
    }

    public function testImplementsCountable()
    {
        $this->assertInstanceOf('Countable', $this->collection);
    }

    public function testCanCountCorrectly()
    {
        $items = [
            'foo' => 'bar'
        ];
        $this->collection = new Collection($items);
        $this->assertSame(1, $this->collection->count());
    }

    public function testImplementsArrayAccess()
    {
        $this->assertInstanceOf('ArrayAccess', $this->collection);
    }

    public function testCanConfirmOffsetExists()
    {
        $items = [
            'foo',
            'bar'
        ];
        $this->collection = new Collection($items);
        $this->assertTrue($this->collection->offsetExists(0));
    }

    public function testCanGetOffset()
    {
        $items = [
            'foo',
            'bar'
        ];
        $this->collection = new Collection($items);
        $this->assertSame('foo', $this->collection->offsetGet(0));
    }

    public function testCanSetOffset()
    {
        $items = [
            'foo',
            'bar'
        ];
        $this->collection = new Collection($items);
        $this->collection->offsetSet(0, 'baz');
        $this->assertSame(['baz', 'bar'], $this->collection->toArray());
        $this->assertSame('baz', $this->collection->offsetGet(0));
    }

    public function testAppendsElementWhenOffsetSetPassedNull()
    {
        $items = [
            'foo',
            'bar'
        ];
        $this->collection = new Collection($items);
        $this->collection->offsetSet(null, 'baz');
        $this->assertSame(['foo', 'bar', 'baz'], $this->collection->toArray());
        $this->assertSame('foo', $this->collection->offsetGet(0));
        $this->assertSame('bar', $this->collection->offsetGet(1));
        $this->assertSame('baz', $this->collection->offsetGet(2));
    }

    public function testCanUnsetOffset()
    {
        $items = [
            'foo',
            'bar'
        ];
        $this->collection = new Collection($items);
        $this->collection->offsetUnset(1);
        $this->assertSame(['foo'], $this->collection->toArray());
        $this->assertSame(1, $this->collection->count());
    }

    public function testImplementsIterator()
    {
        $this->assertInstanceOf('IteratorAggregate', $this->collection);
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf('ArrayIterator', $this->collection->getIterator());
    }

    public function testSupportsMacros()
    {
        $items = [16, 25];
        $collection = new Collection($items);
        $collection->macro('squareRoot', function () use ($collection) {
            return $collection->map(function ($number) {
                return (int)sqrt($number);
            });
        });
        $this->assertSame([4, 5], $collection->squareRoot()->toArray());
    }

    public function testSupportsStaticMacros()
    {
        $items = [16, 25];
        $collection = new Collection($items);
        Collection::macro('squareRoot', function () use ($collection) {
            return $collection->map(function ($number) {
                return (int)sqrt($number);
            });
        });
        $this->assertSame([4, 5], $collection->squareRoot()->toArray());
    }

    public function testSupportsCallingMacrosStatically()
    {
        Collection::macro('bananas', function () {
            return 'bananas';
        });
        $this->assertSame('bananas', Collection::bananas());
    }

    public function testAbsentMacroMethod()
    {
        $this->expectException('BadMethodCallException');
        $items = [16, 25];
        $collection = new Collection($items);
        $collection->foo();
    }

    public function testAbsentMacroMethodStatic()
    {
        $this->expectException('BadMethodCallException');
        Collection::foo();
    }

    public function testMixinFromClass()
    {
        Collection::mixin(new class {
            public function foo()
            {
                return 'Foo';
            }
        });
        $items = [16, 25];
        $collection = new Collection($items);
        $this->assertEquals('Foo', $collection->foo());
    }

    public function testCallMacroStatically()
    {
        Collection::mixin(new class {
            public function foo()
            {
                return 'Foo';
            }
        });
        $items = [16, 25];
        $collection = new Collection($items);
        $this->assertEquals('Foo', Collection::foo());
    }

    public function testCallCallableMacro()
    {
        $callable = new class {
            public function __invoke()
            {
                return 'Foo';
            }
        };
        $items = [16, 25];
        Collection::macro('foo', $callable);
        $collection = new Collection($items);
        $this->assertEquals('Foo', $collection->foo());
    }

    public function testCallCallableMacroStatically()
    {
        $callable = new class {
            public function __invoke()
            {
                return 'Foo';
            }
        };
        $items = [16, 25];
        Collection::macro('foo', $callable);
        $this->assertEquals('Foo', Collection::foo());
    }

    public function testImplementsMap()
    {
        $items = [
            1,
            2,
            3
        ];
        $this->collection = new Collection($items);
        $this->assertSame([1,8,27], $this->collection->map(function ($item) {
            return ($item * $item * $item);
        })->toArray());
    }

    public function testImplementsFilter()
    {
        $items = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3
        ];
        $this->collection = new Collection($items);
        $this->assertSame([
            'bar' => 2,
            'baz' => 3
        ], $this->collection->filter(function ($v) {
            return $v > 1;
        })->toArray());
    }

    public function testImplementsReject()
    {
        $items = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3
        ];
        $this->collection = new Collection($items);
        $this->assertSame([
            'bar' => 2,
            'baz' => 3
        ], $this->collection->reject(function ($v) {
            return $v <= 1;
        })->toArray());
    }

    public function testImplementsReduce()
    {
        $items = [1, 2, 3];
        $this->collection = new Collection($items);
        $this->assertSame(6, $this->collection->reduce(function ($total, $item) {
            return $total += $item;
        }));
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
        $this->collection = new Collection($items);
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
        $this->collection = new Collection([$date]);
        $this->collection->each(function ($item) {
            $item->setTimezone('Europe/London');
        });
    }

    public function testImplementsPush()
    {
        $items = [1, 2, 3];
        $this->collection = new Collection($items);
        $this->assertSame([1, 2, 3, 4], $this->collection->push(4)->toArray());
    }

    public function testImplementsPop()
    {
        $items = [1, 2, 3];
        $this->collection = new Collection($items);
        $this->assertSame(3, $this->collection->pop());
        $this->assertSame([1, 2], $this->collection->toArray());
    }

    public function testImplementsUnshift()
    {
        $items = [1, 2, 3];
        $this->collection = new Collection($items);
        $this->assertSame([4, 1, 2, 3], $this->collection->unshift(4)->toArray());
    }

    public function testImplementsShift()
    {
        $items = [1, 2, 3];
        $this->collection = new Collection($items);
        $this->assertSame(1, $this->collection->shift());
        $this->assertSame([2, 3], $this->collection->toArray());
    }

    public function testImplementsSort()
    {
        $items = [2, 1, 3];
        $this->collection = new Collection($items);
        $this->assertSame([3, 2, 1], $this->collection->sort(function ($a, $b) {
            return ($a > $b) ? -1 : 1;
        })->toArray());
    }

    public function testAllowsACallbackToSort()
    {
        $items = [2, 1, 3];
        $this->collection = new Collection($items);
        $this->assertSame([1, 2, 3], $this->collection->sort()->toArray());
    }
}
