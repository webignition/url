.. code-block:: php

    <?php

    use webignition\Url\Parser;

    $components = Parser::parse('https://example.com:8080/path?query#fragment');

    $components[Parser::COMPONENT_SCHEME];
    // "https"

    $components[Parser::COMPONENT_HOST];
    // "example.com"
