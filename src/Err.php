<?php

declare(strict_types=1);

namespace Auroro\Result;

/**
 * @template E
 * @extends Result<mixed, E>
 */
final readonly class Err extends Result
{
    /** @param E $error */
    public function __construct(public mixed $error) {}

    public function isOk(): bool
    {
        return false;
    }

    public function isErr(): bool
    {
        return true;
    }

    public function unwrap(): never
    {
        throw new \LogicException('Called unwrap() on Err');
    }

    /** @return E */
    public function error(): mixed
    {
        return $this->error;
    }

    /**
     * @return $this
     */
    public function map(callable $fn): Result
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function andThen(callable $fn): Result
    {
        return $this;
    }

    /**
     * @template F
     * @param callable(E): F $fn
     * @return Err<F>
     */
    public function mapError(callable $fn): Result
    {
        return new self($fn($this->error));
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }
}
