.. title:: URL Documentation

=================
URL Documentation
=================

The `webginition/url <https://packagist.org/packages/webignition/url>`_ package models, normalizes, parses and
compares URLs.

---
Url
---

.. include:: includes/url/introduction.rst
.. include:: includes/url/example.rst

Read the :doc:`Url usage </url>` guide for more detail.

----------
Normalizer
----------

.. rst-class:: precede-list
.. include:: includes/normalizer/introduction.rst
.. include:: includes/normalizer/example.rst
.. include:: includes/normalizer/normalizations-list.rst

Read the :doc:`Normalizer usage </normalizer>` guide for more detail.

------
Parser
------

.. include:: includes/parser/introduction.rst
.. include:: includes/parser/example.rst

---------
Inspector
---------

The ``Inspector`` examines ``UriInterface`` instance.

--------------
Scope Comparer
--------------

The ``ScopeComparer`` examines whether two ``UriInterface`` instances are in the same scope.

.. toctree::
   :hidden:
   :caption: First Steps

   getting-started

.. toctree::
    :caption: URL
    :maxdepth: 3

    url

.. toctree::
    :caption: Normalizer
    :maxdepth: 3

    normalizer
