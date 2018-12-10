================================
Applying Multiple Normalizations
================================

You can apply any number of the sixteen normalizations when normalizing a ``UriInterface`` instance.

--------------
Multiple Flags
--------------

Combine flags using the bitwise ``|`` operator.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http://example.com?b=bear&a=apple#fragment');
    $normalizedUrl = Normalizer::normalize(
        $url,
        Normalizer::SORT_QUERY_PARAMETERS | Normalizer::REMOVE_FRAGMENT
    );

    (string) $normalizedUrl;
    // "http://example.com?a=apple&b=bear"

------------------------------------
Options With No Other Normalizations
------------------------------------

To apply one or more options but no other normalizations, call ``Normalizer::normalize()`` with ``Normalizer::NONE`` as
the ``flags`` argument.

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

----------------------------------------------
Apply All Semantically-Lossless Normalizations
----------------------------------------------

A set of normalizations that do not change the semantics of a URL are defined as
``Normalizer::PRESERVING_NORMALIZATIONS``.

Read more about :doc:`semantically-lossless normalizations </semantically-lossless-normalizations>` to see what
flags this applies.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http//♥.example.com:80/p%61th/../?option=%3f');
    $normalizedUrl = Normalizer::normalize($url, Normalizer::PRESERVING_NORMALIZATIONS);

    (string) $normalizedUrl;
    // "http//xn--g6h.example.com:80/path/?option=%3F"

The ``flags`` argument of ``Normalizer::normalize()`` defaults to ``Normalizer::PRESERVING_NORMALIZATIONS``.

The following is equivalent to the above:

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http//♥.example.com:80/p%61th/../?option=%3f');
    $normalizedUrl = Normalizer::normalize($url);

    (string) $normalizedUrl;
    // "http//xn--g6h.example.com:80/path/?option=%3F"