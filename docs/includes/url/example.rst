.. code-block:: php

    <?php

    use webignition\Url\Url;

    $url = new Url('http://example.com/path?query#fragment');

    $url->getScheme();
    // "http"

    $url->getQuery();
    // "query"

    $modifiedUrl = $url
        ->withScheme('https')
        ->withPath('/modified-path')
        ->withQuery('foo=bar')
        ->withFragment('');
    (string) $modifiedUrl;
    // https://example.com/modified-path?foo=bar
