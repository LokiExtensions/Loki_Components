<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Util;

class IdConvertor
{
    public function toElementId(string $text): string
    {
        return preg_replace('#([^a-zA-Z0-9]{1})#', '-', $text);
    }
}
