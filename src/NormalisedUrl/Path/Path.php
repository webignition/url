<?php

namespace webignition\NormalisedUrl\Path;

use webignition\Url\Path\Path as RegularPath;

/**
 * Represents the normalised path part of a URL
 */
class Path extends RegularPath
{
    /**
     * @param string $path
     */
    public function __construct(?string $path)
    {
        parent::__construct($path);

        $normaliser = new Normaliser(parent::get());

        $this->set($normaliser->get());
    }
}
