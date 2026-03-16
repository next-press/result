<?php

declare(strict_types=1);

use Auroro\Result\Err;
use Auroro\Result\Ok;
use Auroro\Result\Result;

use function Auroro\Result\err;
use function Auroro\Result\ok;

// --- Ok ---

it('Ok is ok', function () {
    $result = new Ok(42);

    expect($result->isOk())->toBeTrue();
    expect($result->isErr())->toBeFalse();
});

it('Ok unwraps the value', function () {
    $result = new Ok('hello');

    expect($result->unwrap())->toBe('hello');
});

it('Ok unwraps null value', function () {
    $result = new Ok(null);

    expect($result->unwrap())->toBeNull();
});

it('Ok error() throws LogicException', function () {
    $result = new Ok(42);

    $result->error();
})->throws(LogicException::class, 'Called error() on Ok');

it('Ok map transforms the value', function () {
    $result = new Ok(2);

    $mapped = $result->map(fn (int $v) => $v * 3);

    expect($mapped)->toBeInstanceOf(Ok::class);
    expect($mapped->unwrap())->toBe(6);
});

it('Ok andThen chains into new Result', function () {
    $result = new Ok(10);

    $chained = $result->andThen(fn (int $v) => new Ok($v + 5));

    expect($chained)->toBeInstanceOf(Ok::class);
    expect($chained->unwrap())->toBe(15);
});

it('Ok andThen can produce Err', function () {
    $result = new Ok('');

    $chained = $result->andThen(fn (string $v) => $v === '' ? new Err('empty') : new Ok($v));

    expect($chained)->toBeInstanceOf(Err::class);
    expect($chained->error())->toBe('empty');
});

it('Ok mapError is a no-op', function () {
    $result = new Ok(42);

    $mapped = $result->mapError(fn (string $e) => "wrapped: {$e}");

    expect($mapped)->toBe($result);
});

it('Ok unwrapOr returns the value, not the default', function () {
    $result = new Ok(42);

    expect($result->unwrapOr(0))->toBe(42);
});

// --- Err ---

it('Err is err', function () {
    $result = new Err('fail');

    expect($result->isOk())->toBeFalse();
    expect($result->isErr())->toBeTrue();
});

it('Err error() returns the error', function () {
    $result = new Err('something broke');

    expect($result->error())->toBe('something broke');
});

it('Err unwrap throws LogicException', function () {
    $result = new Err('fail');

    $result->unwrap();
})->throws(LogicException::class, 'Called unwrap() on Err');

it('Err map is a no-op', function () {
    $result = new Err('fail');

    $mapped = $result->map(fn (int $v) => $v * 2);

    expect($mapped)->toBe($result);
});

it('Err andThen is a no-op', function () {
    $result = new Err('fail');

    $chained = $result->andThen(fn (int $v) => new Ok($v * 2));

    expect($chained)->toBe($result);
});

it('Err mapError transforms the error', function () {
    $result = new Err('fail');

    $mapped = $result->mapError(fn (string $e) => "wrapped: {$e}");

    expect($mapped)->toBeInstanceOf(Err::class);
    expect($mapped->error())->toBe('wrapped: fail');
});

it('Err unwrapOr returns the default', function () {
    $result = new Err('fail');

    expect($result->unwrapOr(99))->toBe(99);
});

// --- Helper functions ---

it('ok() creates an Ok', function () {
    $result = ok('value');

    expect($result)->toBeInstanceOf(Ok::class);
    expect($result->unwrap())->toBe('value');
});

it('err() creates an Err', function () {
    $result = err('problem');

    expect($result)->toBeInstanceOf(Err::class);
    expect($result->error())->toBe('problem');
});

// --- Static factories ---

it('Result::try wraps return value in Ok', function () {
    $result = Result::try(fn () => 42);

    expect($result)->toBeInstanceOf(Ok::class);
    expect($result->unwrap())->toBe(42);
});

it('Result::try wraps exception in Err', function () {
    $result = Result::try(fn () => throw new RuntimeException('boom'));

    expect($result)->toBeInstanceOf(Err::class);
    expect($result->error())->toBeInstanceOf(RuntimeException::class);
    expect($result->error()->getMessage())->toBe('boom');
});

it('Result::all returns Ok with all values when all Ok', function () {
    $result = Result::all([ok(1), ok(2), ok(3)]);

    expect($result)->toBeInstanceOf(Ok::class);
    expect($result->unwrap())->toBe([1, 2, 3]);
});

it('Result::all short-circuits on first Err', function () {
    $result = Result::all([ok(1), err('fail'), ok(3)]);

    expect($result)->toBeInstanceOf(Err::class);
    expect($result->error())->toBe('fail');
});

it('Result::all returns Ok([]) for empty array', function () {
    $result = Result::all([]);

    expect($result)->toBeInstanceOf(Ok::class);
    expect($result->unwrap())->toBe([]);
});

it('Result::from returns Ok for non-null value', function () {
    $result = Result::from('hello', 'missing');

    expect($result)->toBeInstanceOf(Ok::class);
    expect($result->unwrap())->toBe('hello');
});

it('Result::from returns Err for null value', function () {
    $result = Result::from(null, 'missing');

    expect($result)->toBeInstanceOf(Err::class);
    expect($result->error())->toBe('missing');
});
