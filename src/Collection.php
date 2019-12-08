<?php

declare(strict_types=1);

namespace PublishingKit\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function count()
    {
        return count($this->items);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function map(callable $callback): Collection
    {
        return new static(array_map($callback, $this->items));
    }
    public function filter(callable $callback): Collection
    {
        return new static(array_filter($this->items, $callback));
    }
}
