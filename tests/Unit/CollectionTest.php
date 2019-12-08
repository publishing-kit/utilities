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
}
