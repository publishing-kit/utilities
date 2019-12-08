<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\SimpleTestCase;
use PublishingKit\Collection\Collection;

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
}
