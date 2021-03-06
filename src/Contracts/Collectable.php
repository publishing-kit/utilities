<?php

declare(strict_types=1);

namespace PublishingKit\Utilities\Contracts;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use OutOfBoundsException;
use Serializable;

/**
 * Collection
 *
 * @template T
 */
interface Collectable extends Countable, IteratorAggregate, JsonSerializable, Serializable
{
    /**
     * Convert collection to J)SON
     *
     * @return string|false
     */
    public function toJson();

    /**
     * Convert collection to array
     *
     * @return iterable
     */
    public function toArray(): iterable;

    /**
     * Map operation
     *
     * @param callable $callback The callback to use.
     * @return Collectable
     */
    public function map(callable $callback);

    /**
     * Filter operation
     *
     * @param callable $callback The callback to use.
     * @psalm-param callable(T): bool $callback
     * @return Collectable
     */
    public function filter(callable $callback);

    /**
     * Reverse filter operation
     *
     * @param callable $callback The callback to use.
     * @psalm-param callable(T): bool $callback
     * @return Collectable
     */
    public function reject(callable $callback);

    /**
     * Reduce operation
     *
     * @param callable $callback The callback to use.
     * @param mixed   $initial  The initial value.
     * @return mixed
     */
    public function reduce(callable $callback, $initial = 0);

    /**
     * Pluck a single field
     *
     * @param mixed $name Name of field to pluck.
     * @return mixed
     */
    public function pluck($name);

    /**
     * Apply callback to each item in the collection
     *
     * @param callable $callback The callback to use.
     * @return void
     */
    public function each(callable $callback);

    /**
     * Return all items
     *
     * @return iterable
     */
    public function all(): iterable;
}
