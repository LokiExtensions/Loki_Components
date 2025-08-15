<?php
declare(strict_types=1);

namespace Loki\Components\Util\Block\CssStyleParser;

use Magento\Framework\View\Element\AbstractBlock;

interface CssStyleParserInterface
{
    public function parse(array $cssStyles, AbstractBlock $block): array;
}
