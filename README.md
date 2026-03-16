# auroro/result

Errors as values: a Result type with `Ok` and `Err` variants for PHP 8.3+.

## Installation

```bash
composer require auroro/result
```

## Usage

```php
use Auroro\Result\Ok;
use Auroro\Result\Err;
use Auroro\Result\Result;

use function Auroro\Result\ok;
use function Auroro\Result\err;
```

### Creating results

```php
$ok  = new Ok(42);
$err = new Err('something went wrong');

// Or use the helper functions
$ok  = ok(42);
$err = err('something went wrong');
```

### Checking and unwrapping

```php
$result->isOk();       // true | false
$result->isErr();      // true | false
$result->unwrap();     // returns value or throws LogicException
$result->error();      // returns error or throws LogicException
$result->unwrapOr(0);  // returns value or the default
```

### Transforming

```php
// Map the success value
ok(2)->map(fn ($v) => $v * 3);  // Ok(6)

// Chain into another Result
ok(10)->andThen(fn ($v) => $v > 0 ? ok($v) : err('negative'));

// Map the error
err('fail')->mapError(fn ($e) => "wrapped: $e");  // Err("wrapped: fail")
```

### Static factories

```php
// Wrap a callable — exceptions become Err
Result::try(fn () => riskyOperation());  // Ok(value) | Err(Throwable)

// Collect results — short-circuits on first Err
Result::all([ok(1), ok(2), ok(3)]);  // Ok([1, 2, 3])
Result::all([ok(1), err('x')]);      // Err('x')

// Null check
Result::from($value, 'missing');  // Ok($value) if non-null, Err('missing') if null
```

## License

MIT
