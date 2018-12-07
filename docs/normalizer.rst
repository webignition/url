==========
Normalizer
==========

.. rst-class:: precede-list
.. include:: includes/normalizer/introduction.rst
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
