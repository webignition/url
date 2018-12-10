# Upgrading to 3.0

## Overview

The 3.0 releases introduce significant changes and is in no way backwards-compatible
with 2.x:

- PHP 7.2+ only
- Url implements [`UriInterface`](https://github.com/php-fig/http-message/blob/master/src/UriInterface.php)
- Remove `NormalisedUrl`, replace with `Normalizer`
- Remove `Query` model

## PHP 7.2+ Only

If you're running a version of PHP older than 7.2, you will have to upgrade. There's no other way.

## Url Implements UriInterface

`Url` now implements the [ PSR-7` UriInterface`](https://github.com/php-fig/http-message/blob/master/src/UriInterface.php).

This is a significant change that improves interoperability with any other package or library that handles ``UrlInterface``
instances.

The most significant change is that a `Url` is now immutable. Methods named `set*()` have been replaced with methods
named `with*()` which return a new instance that has a relevant changed component.

- constructor behaviour remains the same from an outside perspective
- most `get*()` methods remain in place but may have different return types
- `set*()` methods have changed to `with*()`methods (approximately)

| 2.x method | 3.0 Method or Equivalent  |
|---|---|
| `init()` | No alternative |
| `__construct(string $url)` | `__construct(string $url)` |
| `__toString(): string` | `__toString(): string` |
| `getRoot()` | `$root = $url->getScheme() . '://' . $url->getAuthority()` |
| `getScheme(): string` | `getScheme(): string` |
| `getUser(): string` | `(new UserInfo($url->getUserInfo()))->getUser()` |
| `getPass()` | `(new UserInfo($url->getUserInfo()))->getPassword()` |
| `getHost(): Host` | `getHost(): string` |
| `getPort(): int` | `getPort(): int \| null` |
| `getPath(): Path` | `getPath(): string` |
| `getQuery(): Query` | `getQuery(): string` |
| `getFragment(): string` | `getFragment(): string` |
| `setScheme(string $scheme): bool` | `withScheme(string $scheme): Url` |
| `setUser(string $user): bool` | `withUserInfo(string $user, ?string $password): Url` |
| `setPass(?string $pass): bool` | `withUserInfo(string $user, ?string $password): Url` |
| `setHost(string $host`): bool | `withHost(string $host): Url` |
| `setPort(int \| null $port): bool` | `withPort(int \| null $port): Url` |
| `setPath(string $path): bool` | `withPath(string $path): Url` |
| `setQuery(string $query): bool` | `withQuery(string $query): Url` |
| `setFragment(string $fragment): bool` | `withFragment(string $fragment): Url` |
| `setPart(string $partName, $value): bool` | Use part-appropriate `with*()` |
| `hasScheme(): bool` | `'' !== $url->getScheme()` |
| `hasUser(): bool` | `'' !== $url->getUserInfo()` |
| `hasPass(): bool` | `null !== (new UserInfo($url->getUserInfo()))->getPassword()` |
| `hasCredentials()` | `'' !== $url->getUserInfo()` |
| `hasPort(): bool` | `null !== $url->getPort()` |
| `hasHost(): bool` | `'' !== $url->getHost()` |
| `hasPath(): bool` | `'' !== $url->getPath()` |
| `hasFragment(): bool` | `'' !== $url->getFragment()` |
| `isRelative(): bool` | `(new Path($url->getPath(())->isRelative()` |
| `isAbsolute()` | `(new Path($url->getPath(())->isAbsolute()` |
| `isProtocolRelative(): bool` | `Inspector::isProtocolRelative($url)` |
| `isPubliclyRoutable()` | `Inspector::isPubliclyRoutable($url)` |
| `getConfiguration()` | No alternative |

## Remove NormalisedUrl, Replace With Normalizer

Having both `Url` and `NormalisedUrl` classes has started to make less and less sense.

The `NormalisedUrl` class has been removed. A `Normalizer` class has been added to provide
equivalent normalization.

Before:

```php
use webignition\NormalisedUrl\NormalisedUrl;

$normalizedUrl = new NormalisedUrl('http://example.com/?b=bar&a=foo');
echo (string) $normalizedUrl;
// http://example.com/?a=foo&b=bar
```

After:

```php
use webignition\Url\Normalizer;
use webignition\Url\Url;

$url = new Url('http://example.com/?b=bar&a=foo');
$normalizedUrl = Normalizer::normalize($url, Normalizer::SORT_QUERY_PARAMETERS);
echo (string) $normalizedUrl;
// http://example.com/?a=foo&b=bar
```

## Remove Parser Constructor, Remove Parser::getParts()

That the parser took a URL string as a constructor argument rendered a parser useful for parsing
just a single URL.

The constructor and the `getParts()` method have been removed. Call `Parser::parse()` instead.

Before:

```php
$parser = new Parser('http://example.com/');
$urlComponents = $parser->getParts();
```

After:

```php
$parser = new Parser();
$urlComponents = Parser::parse('http://example.com/);
```

## Remove Query Model

The `Query` class was untenable.
 
With a query being permitted to contain repeat occurrences of the same key, it is not feasible to form a model 
of key:value pairs in a manner that allows the original un-parsed string representation to be re-formed in an 
identical manner.
