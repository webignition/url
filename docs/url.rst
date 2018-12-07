=========
URL Model
=========

.. include:: includes/url/introduction.rst

.. rst-class:: precede-list

    Minimal non-optional RFC 3986 normalization is applied by default:

- converts scheme to lowercase
- converts host to lowercase
- removes the default port

--------------
Creating a URL
--------------

A new ``Url`` instance is created by passing a URL string to the constructor:

.. code-block:: php

    <?php

    use webignition\Url\Url;

    $url = new Url('https://example.com');

----------------
Component Access
----------------

.. code-block:: php

    <?php

    use webignition\Url\Url;

    $url = new Url('https://user:password@example.com:8080/path?query#fragment');

    $url->getScheme();
    // "https"

    $url->getUserInfo();
    // "user:password"

    $url->getHost();
    // "example.com"

    $url->getPort();
    // 8080

    $url->getAuthority();
    // "user:password@example.com:8080"

    $url->getPath();
    // "/path"

    $url->getQuery();
    // "query"

    $url->getFragment();
    // "fragment"

----------------------
Component Modification
----------------------

The ``Url::with*()`` are used to set components. A ``Url`` is immutable. The return value is a new ``Url`` instance.

.. code-block:: php

    <?php

    use webignition\Url\Url;

    $url = new Url('https://user:password@example.com:8080/path?query#fragment');
    (string) $url;
    // "https://user:password@example.com:8080/path?query#fragment"

    $url = $url->withScheme('http');
    (string) $modifiedUrl;
    // "http://user:password@example.com:8080/path?query#fragment"

    $url = $url->withUserInfo('new-user', 'new-password');
    (string) $modifiedUrl;
    // "http://new-user:new-password@example.com:8080/path?query#fragment"

    $url = $url->withUserInfo('');
    (string) $modifiedUrl;
    // "http://example.com:8080/path?query#fragment"

    $url = $url->withHost('new.example.com');
    (string) $modifiedUrl;
    // "http://new.example.com:8080/path?query#fragment"

    $url = $url->withPort(null);
    (string) $modifiedUrl;
    // "http://new.example.com/path?query#fragment"

    $url = $url->withPath('');
    (string) $modifiedUrl;
    // "http://new.example.com?query#fragment"

    $url = $url->withQuery('');
    (string) $modifiedUrl;
    // "http://new.example.com#fragment"

    $url = $url->withFragment('');
    (string) $modifiedUrl;
    // "http://new.example.com"

--------------------------
Non-Optional Normalization
--------------------------

.. code-block:: php

    <?php

    use webignition\Url\Url;

    $url = new Url('HTTPS://EXAMPLE.com:443');

    $url->getScheme();
    // "https"

    $url->getHost();
    // "example.com"

    $url->getPort();
    // null
