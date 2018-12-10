====================================
Semantically-Lossless Normalizations
====================================

.. rst-class:: precede-list

There is a set of normalizations that do not change the semantics of a URL. These are defined as
``Normalizer::PRESERVING_NORMALIZATIONS``. The normalizer applies this set of normalizations if no specific
normalizations are requested.

- :ref:`capitalize percent encoding <normalizations-capitalize-percent-encoding>`
- :ref:`decode unreserved characters <normalizations-decode-unreserved-characters>`
- :ref:`convert empty http path <normalizations-convert-empty-http-path>`
- :ref:`remove default file host <normalizations-remove-default-file-host>`
- :ref:`remove port host <normalizations-remove-default-port>`
- :ref:`remove path dot segments <normalizations-remove-path-dot-segments>`
- :ref:`convert host unicode to punycode <normalizations-convert-host-unicode-to-punycode>`

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http//♥.example.com:80/p%61th/../?option=%3f');
    $normalizedUrl = Normalizer::normalize($url);

    (string) $normalizedUrl;
    // "http//xn--g6h.example.com:80/path/?option=%3F"

The ``Normalizer::PRESERVING_NORMALIZATIONS`` flag can be used in conjunction with additional normalizations.

.. code-block:: php

    <?php

    use webignition\Url\Normalizer;
    use webignition\Url\Url;

    $url = new Url('http//♥.example.com:80/p%61th/../?option=%3f&b=bear&a-apple');
    $normalizedUrl = Normalizer::normalize(
        $url,
        Normalizer::PRESERVING_NORMALIZATIONS |
        Normalizer::SORT_QUERY_PARAMETERS
    );

    (string) $normalizedUrl;
    // "http//xn--g6h.example.com:80/path/?a=apple&bear&option=%3F"