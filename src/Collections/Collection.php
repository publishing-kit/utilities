<?php

declare(strict_types=1);

namespace PublishingKit\Utilities\Collections;

use Countable;
use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;
use JsonSerializable;
use Serializable;
use PublishingKit\Utilities\Contracts\Collectable;
use PublishingKit\Utilities\Traits\Macroable;

/**
 * Collection class
 *
 * @psalm-consistent-constructor
 * @template T
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate, JsonSerializable, Collectable, Serializable
{
    use Macroable;

    /**
     * Items
     *
     * @var iterable<T>
     */
    protected $items;

    /**
     * Position
     *
     * @var integer
     */
    protected $position = 0;

    /**
     * Constructor
     *
     * @param iterable<T> $items Items to collect.
     * @return void
     */
    public function __construct(iterable $items = [])
    {
        $this->items = $items;
    }

    /**
     * Create collection
     *
     * @param iterable $items Items to collect.
     * @return Collection
     */
    public static function make(iterable $items)
    {
        return new static($items);
    }

    /**
     * Return count of items
     *
     * @return integer
     */
    public function count()
    {
        if (is_array($this->items)) {
            return count($this->items);
        }
        $count = 0;
        foreach ($this->items as $item) {
            $count++;
        }
        return $count;
    }

    /**
     * Does item exist?
     *
     * @param mixed $offset The offset.
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Get offset
     *
     * @param mixed $offset The offset.
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Set offset
     *
     * @param mixed $offset The offset.
     * @param mixed $value  The value to set.
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
            return;
        }
        $this->items[$offset] = $value;
    }

    /**
     * Unset offset
     *
     * @param mixed $offset The offset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Serialize collection to JSON
     *
     * @return string|false
     */
    public function jsonSerialize()
    {
        return json_encode($this->items);
    }

    /**
     * Convert collection to JSON
     *
     * @return string|false
     */
    public function toJson()
    {
        return $this->jsonSerialize();
    }

    /**
     * Convert collection to array
     *
     * @return iterable
     */
    public function toArray(): iterable
    {
        return $this->items;
    }

    /**
     * Map operation
     *
     * @param callable $callback The callback to use.
     * @return Collection
     */
    public function map(callable $callback)
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * Filter operation
     *
     * @param callable $callback The callback to use.
     * @return Collection
     */
    public function filter(callable $callback)
    {
        return new static(array_filter($this->items, $callback));
    }

    /**
     * Reverse filter operation
     *
     * @param callable $callback The callback to use.
     * @return Collection
     */
    public function reject(callable $callback)
    {
        return $this->filter(function ($item) use ($callback) {
            return !$callback($item);
        });
    }

    /**
     * Reduce operation
     *
     * @param callable $callback The callback to use.
     * @param mixed   $initial  The initial value.
     * @return mixed
     */
    public function reduce(callable $callback, $initial = 0)
    {
        $accumulator = $initial;
        foreach ($this->items as $item) {
            $accumulator = $callback($accumulator, $item);
        }
        return $accumulator;
    }

    /**
     * Reduce operation that returns a collection
     *
     * @param callable $callback The callback to use.
     * @param mixed   $initial  The initial value.
     * @return Collection
     */
    public function reduceToCollection(callable $callback, $initial = 0): Collection
    {
        $accumulator = $initial;
        foreach ($this->items as $item) {
            $accumulator = $callback($accumulator, $item);
        }
        return new static($accumulator);
    }

    /**
     * Pluck a single field
     *
     * @param mixed $name Name of field to pluck.
     * @return mixed
     */
    public function pluck($name)
    {
        return $this->map(function (array $item) use ($name) {
            return $item[$name];
        });
    }

    /**
     * Apply callback to each item in the collection
     *
     * @param callable $callback The callback to use.
     * @return void
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $item) {
            $callback($item);
        }
    }

    /**
     * Push item to end of collection
     *
     * @param mixed $item Item to push.
     * @return Collection
     */
    public function push($item)
    {
        array_push($this->items, $item);
        return new static($this->items);
    }

    /**
     * Pop item from end of collection
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Push item to start of collection
     *
     * @param mixed $item Item to push.
     * @return Collection
     */
    public function unshift($item)
    {
        array_unshift($this->items, $item);
        return new static($this->items);
    }

    /**
     * Pop item from start of collection
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Sort collection
     *
     * @param callable|null $callback The callback to use.
     * @return Collection
     */
    public function sort(callable $callback = null)
    {
        if ($callback) {
            usort($this->items, $callback);
        } else {
            sort($this->items);
        }
        return new static($this->items);
    }

    /**
     * Reverse collection
     *
     * @return Collection
     */
    public function reverse()
    {
        return new static(array_reverse($this->items));
    }

    /**
     * Return keys
     *
     * @return Collection
     */
    public function keys()
    {
        return new static(array_keys($this->items));
    }

    /**
     * Return values
     *
     * @return Collection
     */
    public function values(): Collection
    {
        return new static(array_values($this->items));
    }

    /**
     * Return chunked collection
     *
     * @param integer $size Chunk size.
     * @return Collection
     */
    public function chunk(int $size): Collection
    {
        return new static(array_chunk($this->items, $size));
    }

    /**
     * Merge another array into the collection
     *
     * @param mixed $merge Array to merge.
     * @return Collection
     */
    public function merge($merge): Collection
    {
        return new static(array_merge($this->items, $merge));
    }

    /**
     * Group by a given key
     *
     * @param string $key Key to group by.
     * @return Collection
     */
    public function groupBy(string $key): Collection
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[$item[$key]][] = $item;
        }
        return new static($items);
    }

    /**
     * Flatten items
     *
     * @return Collection
     */
    public function flatten(): Collection
    {
        $return = [];
        array_walk_recursive($this->items, function ($a) use (&$return) {
            $return[] = $a;
        });
        return new static($return);
    }

    /**
     * Paginate items
     *
     * @return Collection
     */
    public function paginate(int $perPage, int $page): Collection
    {
        $offset = ($page - 1) * $perPage;
        return new static(array_slice($this->items, $offset, $perPage));
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $this->items = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function pipe(callable $callback)
    {
        return $callback($this);
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable
     */
    public function all(): iterable
    {
        return $this->toArray();
    }
}
