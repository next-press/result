<?php

declare(strict_types=1);

namespace Auroro\Result;

if (!\function_exists('Auroro\Result\ok')) {
    /**
     * @template T
     * @param T $value
     * @return Ok<T>
     */
    function ok(mixed $value): Ok
    {
        return new Ok($value);
    }

    /**
     * @template E
     * @param E $error
     * @return Err<E>
     */
    function err(mixed $error): Err
    {
        return new Err($error);
    }
}
