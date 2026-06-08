<?php declare(strict_types=1);

namespace Loki\Components\Util;

class SkipValidation
{
    private bool $skipValidation = false;

    public function isSkipValidation(): bool
    {
        return $this->skipValidation;
    }

    public function setSkipValidation(bool $skipValidation): void
    {
        $this->skipValidation = $skipValidation;
    }
}
