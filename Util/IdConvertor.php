<?php
declare(strict_types=1);

namespace Loki\Components\Util;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class IdConvertor implements ArgumentInterface
{
    public function toElementId(string $text): string
    {
        $text = strtolower($text);
        return preg_replace('#([^a-zA-Z0-9]{1})#', '-', $text);
    }
}
