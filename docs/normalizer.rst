==========
Normalizer
==========

.. include:: includes/normalizer/introduction.rst

Normalizations are specified through either `flags` or `options`. Flags are for normalizations that can be turned or
turned off (you either want it or you don't). Options are for normalizations that act on one or more variables that
you get to choose.

.. include:: includes/normalizer/normalizations-list.rst

.. _normalizations-capitalize-percent-encoding:

---------------------------
Capitalize Percent Encoding
---------------------------

Convert percent-encoded triplets (such as ``%3A``) to uppercase. Letters within a percent-encoded triplet are
case-insensitive.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com/path%2fvalue');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::CAPITALIZE_PERCENT_ENCODING);

    (string) $normalizedUrl;
    // "http://example.com/path%2Fvalue"

.. _normalizations-decode-unreserved-characters:

----------------------------
Decode Unreserved Characters
----------------------------

Convert percent-encoded characters that have no special meaning to their unencoded equivalents.

Decodes encoded forms of: ``ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~``

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com/%75%72%6C');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::DECODE_UNRESERVED_CHARACTERS);

    (string) $normalizedUrl;
    // "http://example.com/url"

.. _normalizations-convert-empty-http-path:

-----------------------
Convert Empty HTTP Path
-----------------------

Applies a path of ``/`` where the path is empty and the scheme is ``http`` or ``https``.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::CONVERT_EMPTY_HTTP_PATH);

    (string) $normalizedUrl;
    // "http://example.com/"

.. _normalizations-remove-default-file-host:

------------------------
Remove Default File Host
------------------------

Removes the host of ``localhost`` from a ``file://`` url.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('file://localhost/path');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REMOVE_DEFAULT_FILE_HOST);

    (string) $normalizedUrl;
    // "file:///path"

.. _normalizations-remove-default-port:

-------------------
Remove Default Port
-------------------

Removes the port if it matches the default port for the scheme.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com:80');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REMOVE_DEFAULT_PORT);

    (string) $normalizedUrl;
    // "http://example.com"

    $url = new Url('https://example.com:443');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REMOVE_DEFAULT_PORT);
    (string) $normalizedUrl;
    // "https://example.com"

.. _normalizations-remove-path-dot-segments:

------------------------
Remove Path Dot Segments
------------------------

The ``.`` and ``..`` path segments have a special meaning. These segments are removed and the path
is re-written to be equivalent.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com/a/b/c/./../../g');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REMOVE_PATH_DOT_SEGMENTS);

    (string) $normalizedUrl;
    // "http://example.com/a/g"

.. _normalizations-convert-host-unicode-to-punycode:

--------------------------------
Convert Host Unicode to Punycode
--------------------------------

Unicode hosts containing non-ascii characters are converted to the
`punycode <https://en.wikipedia.org/wiki/Punycode>`_ equivalent.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://♥.example.com/');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::CONVERT_HOST_UNICODE_TO_PUNYCODE);

    (string) $normalizedUrl;
    // "http://xn--g6h.example.com/"

.. _normalizations-reduce-duplicate-path-slashes:

-----------------------------
Reduce Duplicate Path Slashes
-----------------------------

Reduces occurrences of multiple slashes in the path to single slashes.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com///path//');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REDUCE_DUPLICATE_PATH_SLASHES);

    (string) $normalizedUrl;
    // "http://example.com/path/"

.. _normalizations-sort-query-parameters:

---------------------
Sort Query Parameters
---------------------

Alphabetically sorts query parameters by key.

Sorting is neither locale- nor unicode-aware. The purpose is to be able to compare URLs in a reproducible way.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com?c=cow&a=apple&b=bear');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::SORT_QUERY_PARAMETERS);

    (string) $normalizedUrl;
    // "http://example.com?a=apple&b=bear&c=cow"

.. _normalizations-add-path-trailing-slash:

-----------------------
Add Path Trailing Slash
-----------------------

Add a trailing slash to the path if not present.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::ADD_PATH_TRAILING_SLASH);

    (string) $normalizedUrl;
    // "http://example.com/"

.. _normalizations-remove-user-info:

----------------
Remove User Info
----------------

Remove user credentials.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://user:password@example.com');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REMOVE_USER_INFO);

    (string) $normalizedUrl;
    // "http://example.com"

.. _normalizations-remove-fragment:

---------------
Remove Fragment
---------------

Remove fragment component.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com#fragment');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REMOVE_FRAGMENT);

    (string) $normalizedUrl;
    // "http://example.com"

.. _normalizations-remove-www:

---------------------
Remove www Sub-domain
---------------------

Remove the www sub-domain.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://www.example.com');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::REMOVE_WWW);

    (string) $normalizedUrl;
    // "http://example.com"

.. _normalizations-default-scheme:

------------------------
Specify a Default Scheme
------------------------

Specify a default scheme to be applied if none is present.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('//www.example.com');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::NONE, [
        Normalizer::OPTION_DEFAULT_SCHEME => 'http',
    ]);

    (string) $normalizedUrl;
    // "http://example.com"

.. _normalizations-remove-filenames-from-path-by-pattern:

-------------------------------------
Remove Filenames From Path By Pattern
-------------------------------------

Remove the filename from the path component. Removal is defined through one or more patterns.

Useful for stripping common default filenames such as ``index.html``, ``index.js`` or ``default.asp``.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http//www.example.com/index.html');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::NONE, [
        Normalizer::OPTION_REMOVE_PATH_FILES_PATTERNS => Normalizer::REMOVE_INDEX_FILE_PATTERN,
    ]);

    (string) $normalizedUrl;
    // "http://example.com/"

.. _normalizations-remove-query-parameters-by-pattern:

----------------------------------
Remove Query Parameters By Pattern
----------------------------------

Remove query parameters where the parameter key matches one of a set of patterns.

Useful for stripping query parameters considered by you to be irrelevant to the canonical form of a URL.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http//www.example.com?x=1&y=2&utm_source=facebook&utm_medium=18');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::NONE, [
        Normalizer::OPTION_REMOVE_QUERY_PARAMETERS_PATTERNS => [
            '/^utm_\w+/i',
        ],
    ]);

    (string) $normalizedUrl;
    // "http://example.com?page=1&category=2"
