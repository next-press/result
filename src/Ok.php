<?php

declare(strict_types=1);

namespace Auroro\Result;

/**
 * @template T
 * @extends Result<T, never>
 */
final readonly class Ok extends Result
{
    /** @param T $value */
    public function __construct(public mixed $value) {}

    public function isOk(): bool
    {
        return true;
    }

    public function isErr(): bool
    {
        return false;
    }

    /** @return T */
    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function error(): never
    {
        throw new \LogicException('Called error() on Ok');
    }

    /**
     * @template U
     * @param callable(T): U $fn
     * @return Ok<U>
     */
    public function map(callable $fn): Result
    {
        return new self($fn($this->value));
    }

    /**
     * @template U
     * @param callable(T): Result<U, never> $fn
     * @return Result<U, never>
     */
    public function andThen(callable $fn): Result
    {
        return $fn($this->value);
    }

    /**
     * @return $this
     */
    public function mapError(callable $fn): Result
    {
        return $this;
    }

    /** @return T */
    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }
}
