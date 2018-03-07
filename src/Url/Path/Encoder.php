<?php

namespace webignition\Url\Path;

class Encoder
{
    public function encode(Path $path)
    {
        $parts = explode(Path::PATH_PART_SEPARATOR, $path->get());
        foreach ($parts as $partIndex => $part) {
            $parts[$partIndex] = rawurlencode($part);
        }

        return new Path(implode(Path::PATH_PART_SEPARATOR, $parts));
    }
}
