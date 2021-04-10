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

    /**
     * Handle path in a platform-independent way
     *
     * @return self
     */
    public function path(): self;
}
