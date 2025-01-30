<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Util\Block\CssClassParser;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * @todo: Implement this for removing duplicate values
 * - text-2xl + text-xl = text-xl
 * - px-4 + px-5 = px-5
 * @todo: Implement this to sort CSS classes by recommended ordering
 */
interface CssClassParserInterface
{
    public function parse(array $cssClasses, string $scope, AbstractBlock $block): array;
}
