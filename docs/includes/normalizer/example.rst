.. code-block:: php

    <?php

    use webignition\Url\Normalizer;

    $url = new Url('http://example.com/path?c=cow&a=apple&b=bear#fragment');

    $normalizedUrl = Normalizer::normalize(
        $url,
        Normalizer::SORT_QUERY_PARAMETERS | Normalizer::REMOVE_FRAGMENT
    );

    (string) $normalizedUrl;
    // "http://example.com/path?a=apple&b=bear&c=cow"
