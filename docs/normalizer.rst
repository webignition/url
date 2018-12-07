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
