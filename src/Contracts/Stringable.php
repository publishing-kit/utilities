<?php

namespace PublishingKit\Utilities\Contracts;

interface Stringable
{
    /**
     * Create string
     *
     * @param string $string String to use.
     * @return self
     */
    public static function make(string $string): self;

    /**
     * Return count of characters
     *
     * @return integer
     */
    public function count(): int;

    /**
     * Does item exist?
     *
     * @param mixed $offset The offset.
     * @return boolean
     */
    public function offsetExists($offset): bool;

    /**
     * Get offset
     *
     * @param mixed $offset The offset.
     * @return mixed
     */
    public function offsetGet($offset);

    /**
     * Set offset
     *
     * @param mixed $offset The offset.
     * @param mixed $value  The value to set.
     * @return void
     */
    public function offsetSet($offset, $value);

    /**
     * Unset offset
     *
     * @param mixed $offset The offset.
     * @return void
     */
    public function offsetUnset($offset);

    /**
     * Get current item
     *
     * @return mixed
     */
    public function current();

    /**
     * Get key for current item
     *
     * @return mixed
     */
    public function key();

    /**
     * Move counter to next item
     *
     * @return void
     */
    public function next();

    /**
     * Move counter back to zero
     *
     * @return void
     */
    public function rewind();

    /**
     * Is current item valid?
     *
     * @return boolean
     */
    public function valid(): bool;

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Find and replace text
     *
     * @param string $find    Text to find.
     * @param string $replace Text to replace.
     * @return self
     */
    public function replace(string $find, string $replace): self;

    /**
     * Convert to upper case
     *
     * @return self
     */
    public function toUpper(): self;

    /**
     * Convert to lower case
     *
     * @return self
     */
    public function toLower(): self;

    /**
     * Trim whitespace
     *
     * @return self
     */
    public function trim(): self;

    /**
     * Trim left whitespace
     *
     * @return self
     */
    public function ltrim(): self;

    /**
     * Trim right whitespace
     *
     * @return self
     */
    public function rtrim(): self;
}
