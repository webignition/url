<?php

namespace webignition\Url;

use Mso\IdnaConvert\IdnaConvert;

class PunycodeEncoder
{
    /**
     * @var IdnaConvert
     */
    private $idnaConverter;

    public function __construct()
    {
        $this->idnaConverter = new IdnaConvert();
    }

    public function encode(string $value): string
    {
        try {
            return $this->idnaConverter->encode($value);
        } catch (\InvalidArgumentException $invalidArgumentException) {
        }

        return $value;
    }

    public function decode(string $value): string
    {
        return $this->idnaConverter->decode($value);
    }
}
