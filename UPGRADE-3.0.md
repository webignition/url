Upgrading to 3.X
================

Overview
--------

The 3.x releases introduce some significant changes:

- PHP 7.2+ only
- Remove return value from most URL part setters

PHP 7.2+ Only
-------------

If you're running a version of PHP older than 7.2, you will have to upgrade. There's no other way.

Remove Return Value From Most URL Part Setters
----------------------------------------------

The following methods previously always returned `true`. This was unnecessary. These methods
no longer return anything:

- `UrlInterface::setFragment()`
- `UrlInterface::setHost()`
