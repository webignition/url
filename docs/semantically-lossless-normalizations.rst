====================================
Semantically-Lossless Normalizations
====================================

The ``Normalizer``, by default, applies a set of semantically-lossless normalizations if no specific normalizations
are requested.

This set of normalizations is defined by the ``Normalizer::PRESERVING_NORMALIZATIONS`` flag which can be used
in conjunction with additional normalizations.

.. _normalizations-capitalize-percent-encoding-foo:

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