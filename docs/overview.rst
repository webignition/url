========
Overview
========

.. include:: includes/overview/introduction.rst

.. rst-class:: precede-list

    ``Url``

- models a `URL <https://developer.mozilla.org/en-US/docs/Glossary/URL>`_
- provides component access and modification via the `PSR7 UriInterface <https://www.php-fig.org/psr/psr-7/#35-psrhttpmessageuriinterface>`_

.. rst-class:: precede-list

    ``Normalizer``

- offers fifteen normalizations that can be applied to any ``UriInterface`` implementation
- bar

.. rst-class:: precede-list

    ``Parser``

- foo
- bar

.. rst-class:: precede-list

    ``Inspector``

- foo
- bar

.. rst-class:: precede-list

    ``ScopeComparer``

- foo
- bar

---
URL
---

Components of a URL can be accessed as per the
`PSR7 UriInterface <https://www.php-fig.org/psr/psr-7/#35-psrhttpmessageuriinterface>`_.

.. rst-class:: precede-list

    Minimal non-optional RFC 3986 normalization is applied by default:

- converts scheme to lowercase
- converts host to lowercase
- removes the default port

----------
Normalizer
----------

There are fifteen normalizations that can be applied to anything implementing the PSR7 ``UriInterface``.

.. rst-class:: precede-list

    Applies semantically-safe normalization by default:

- capitalizes percent-encoding
- decodes unreserved characters
- converts empty http path
- removes the default file host
- removes the default port
- removes path dot segments
- converts unicode hosts to punycode

Optionally apply normalization for comparisons:

- apply a default scheme if none is present
- force http scheme
- force https scheme
- remove user credentials
- convert unicode to punycode in host
- remove the fragment
- remove the www sub-domain
- remove default filenames by pattern
- remove path dot segments
- add a trailing slash to the path
- sort query parameters

Applies semantically-lossless normalisation for comparisons:

- scheme and host case is ignored
- percent-encoded entities are capitalised
- decodes percent-encoded representations of unreserved characters
- trailing / added to directory-ending URLs
- default port removed
- dot segments (/./ and /../) are removed
- query string arguments are sorted by key name
- IDN host names are normalised to the ASCII variant