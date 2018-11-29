Upgrading to 3.X
================

Overview
--------

The 3.x releases introduce some significant changes:

- PHP 7.2+ only
- Remove `NormalisedUrl`, replace with `Normalizer`
- Remove Parser constructor, remove Parser::getParts()
- Remove return value from most URL part setters

PHP 7.2+ Only
-------------

If you're running a version of PHP older than 7.2, you will have to upgrade. There's no other way.

Remove `NormalisedUrl`, Replace With `Normalizer`
-------------------------------------------------

Having both `Url` and `NormalisedUrl` classes has started to make less and less sense.

The `NormalisedUrl` class has been removed. A `Normalizer` class has been added to provide
equivalent normalization.

Before:

```
php

use webignition\NormalisedUrl\NormalisedUrl;

$normalizedUrl = new NormalisedUrl('http://example.com/?b=bar&a=foo');
echo (string) $normalizedUrl;
// http://example.com/?a=foo&b=bar
```

After:

```
php

use webignition\Url\Normalizer;
use webignition\Url\Url;

$normalizer = new Normalizer();
$url = new Url('http://example.com/?b=bar&a=foo');
$normalizedUrl = $normalizer->normalize($url);
echo (string) $normalizedUrl;
// http://example.com/?a=foo&b=bar
```

Remove Parser Constructor, Remove Parser::getParts()
----------------------------------------------------

That the parser took a URL string as a constructor argument rendered a parser useful for parsing
just a single URL.

The constructor and the `getParts()` method have been removed. Call `parse()` instead.

Before:

```php
$parser = new Parser('http://example.com/');
$urlParts = $parser->getParts();
```

After:

```php
$parser = new Parser();
$urlParts = $parser->parse('http://example.com/);
```


Remove Return Value From Most URL Part Setters
----------------------------------------------

The following methods previously always returned `true`. This was unnecessary. These methods
no longer return anything:

- `UrlInterface::setFragment()`
- `UrlInterface::setHost()`
- `UrlInterface::setPath()`
- `UrlInterface::setQuery()`
- `UrlInterface::setScheme()`
