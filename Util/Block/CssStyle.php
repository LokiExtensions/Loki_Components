<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use Magento\Framework\View\Element\Template;
use Loki\Components\Util\Block\CssStyleParser\CssStyleParserInterface;

class CssStyle
{
    private ?Template $block = null;

    /**
     * @param CssStyleParserInterface[] $cssStyleParsers
     */
    public function __construct(
        private readonly array $cssStyleParsers = [],
    ) {
    }

    public function setBlock(Template $block): CssStyle
    {
        $this->block = $block;

        return $this;
    }

    public function __invoke(string $defaultCss = '', string $scope = 'block'): string
    {
        $cssStyles = (array)$this->block->getData('css_styles');
        $cssStyles = $this->parse($cssStyles);

        $css = '';
        foreach ($cssStyles as $cssStyleName => $cssStyleValue) {
            $css .= $cssStyleName . ':' . $cssStyleValue . ';';
        }

        return trim($css);
    }

    private function parse(array $cssStyles): array
    {
        foreach ($this->cssStyleParsers as $cssStyleParser) {
            if (false === $cssStyleParser instanceof CssStyleParserInterface) {
                continue;
            }

            $cssStyles = $cssStyleParser->parse($cssStyles, $this->block);
        }

        return $cssStyles;
    }
}
