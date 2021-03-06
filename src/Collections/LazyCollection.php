<?php

declare(strict_types=1);

namespace PublishingKit\Utilities\Collections;

use ArrayIterator;
use Generator;
use JsonSerializable;
use Traversable;
use PublishingKit\Utilities\Contracts\Collectable;
use PublishingKit\Utilities\Traits\Macroable;

/**
 * Lazy collection class
 *
 * @psalm-consistent-constructor
 */
class LazyCollection implements Collectable
{
    use Macroable;

    /**
     * @var callable|static
     */
    private $source;

    /**
     * Create a new lazy collection instance.
     *
     * @param  mixed  $source
     * @return void
     */
    public function __construct($source = null)
    {
        if (is_callable($source) || $source instanceof self) {
            $this->source = $source;
        } elseif (is_null($source)) {
            $this->source = static::empty();
        } else {
            $this->source = $this->getArrayableItems($source);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public static function make(callable $callback): self
    {
        return new static($callback);
    }

    /**
     * Create a new instance with no items.
     *
     * @return static
     */
    public static function empty()
    {
        return new static([]);
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param mixed  $items
     *
     * @return iterable
     */
    protected function getArrayableItems($items): iterable
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof Collectable) {
            return $items->toArray();
        } elseif ($items instanceof JsonSerializable) {
            return (array) $items->jsonSerialize();
        } elseif ($items instanceof Traversable) {
            return iterator_to_array($items);
        }
        return (array) $items;
    }

    /**
     * {@inheritDoc}
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return iterable
     */
    public function toArray(): iterable
    {
        return $this->map(function ($value) /* @return mixed */ {
            return $value instanceof Collectable ? $value->toArray() : $value;
        })->all();
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable
     */
    public function all(): iterable
    {
        if (is_array($this->source)) {
            return $this->source;
        }
        return iterator_to_array($this->getIterator());
    }

    /**
     * {@inheritDoc}
     */
    public function map(callable $callback)
    {
        return new static(function () use ($callback) {
            foreach ($this as $key => $value) {
                yield $key => $callback($value, $key);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function filter(callable $callback = null)
    {
        if (is_null($callback)) {
            $callback = function ($value): bool {
                return (bool) $value;
            };
        }
        return new static(function () use ($callback) {
            foreach ($this as $key => $value) {
                if ($callback($value, $key)) {
                    yield $key => $value;
                }
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function reject(callable $callback)
    {
        return $this->filter(function ($item) use ($callback) {
            return !$callback($item);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function reduce(callable $callback, $initial = 0)
    {
        $result = $initial;
        foreach ($this as $value) {
            $result = $callback($result, $value);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function pluck($name)
    {
        return $this->map(function ($item) use ($name) {
            return $item[$name];
        });
    }

    /**
     * {@inheritDoc}
     */
    public function each(callable $callback)
    {
        foreach ($this->source as $item) {
            $callback($item);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->makeIterator($this->source);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return json_encode($this->toArray());
    }

    /**
     * Make an iterator from the given source.
     *
     * @param  mixed  $source
     * @return \Traversable
     */
    protected function makeIterator($source)
    {
        if (is_array($source)) {
            return new ArrayIterator($source);
        }
        return $source();
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $this->source = unserialize($serialized);
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }
}
