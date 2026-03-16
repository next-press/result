<?php

declare(strict_types=1);

namespace Auroro\Result;

/**
 * @template T
 * @template E
 */
abstract readonly class Result
{
    abstract public function isOk(): bool;

    abstract public function isErr(): bool;

    /** @return T */
    abstract public function unwrap(): mixed;

    /** @return E */
    abstract public function error(): mixed;

    /**
     * @template U
     * @param callable(T): U $fn
     * @return Result<U, E>
     */
    abstract public function map(callable $fn): self;

    /**
     * @template U
     * @param callable(T): Result<U, E> $fn
     * @return Result<U, E>
     */
    abstract public function andThen(callable $fn): self;

    /**
     * @template F
     * @param callable(E): F $fn
     * @return Result<T, F>
     */
    abstract public function mapError(callable $fn): self;

    /**
     * @param T $default
     * @return T
     */
    abstract public function unwrapOr(mixed $default): mixed;

    /**
     * @return Result<mixed, \Throwable>
     */
    public static function try(callable $fn): self
    {
        try {
            return new Ok($fn());
        } catch (\Throwable $e) {
            return new Err($e);
        }
    }

    /**
     * @param array<Result<mixed, mixed>> $results
     * @return Result<list<mixed>, mixed>
     */
    public static function all(array $results): self
    {
        $values = [];
        foreach ($results as $result) {
            if ($result->isErr()) {
                return $result;
            }
            $values[] = $result->unwrap();
        }
        return new Ok($values);
    }

    /**
     * @param mixed $value
     * @param mixed $error
     * @return self<mixed, mixed>
     */
    public static function from(mixed $value, mixed $error): self
    {
        return $value !== null ? new Ok($value) : new Err($error);
    }
}
