<?php

namespace webignition\Url;

use webignition\Url\Path\Encoder as PathEncoder;

class Encoder {
    
    public function encode(Url $url) {
        $encodedUrl = clone $url;
        
        if ($encodedUrl->hasPath()) {
            $pathEncoder = new PathEncoder();
            $encodedUrl->setPath((string)$pathEncoder->encode($encodedUrl->getPath()));            
        }
        
        return $encodedUrl;
    }
}