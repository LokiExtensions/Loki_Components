<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\File as AssetFile;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SimpleXMLElement;

class ImageRenderer implements ArgumentInterface
{
    private ?Filesystem\Directory\ReadInterface $fileDriver = null;

    private ?AbstractBlock $block = null;

    public function __construct(
        Filesystem $filesystem,
        private readonly AssetRepository $assetRepository,
        private readonly DirectoryList $directoryList,
        private readonly AppState $appState,
        private readonly string $iconSet = 'default',
        private readonly string $iconPrefix = 'Loki_Components::icon',
        private readonly int $iconSize = 20,
    ) {
        $this->fileDriver = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    public function setBlock(AbstractBlock $block): ImageRenderer
    {
        $this->block = $block;
        return $this;
    }

    public function get(string $imageId, array $attributes = []): string
    {
        $asset = $this->assetRepository->createAsset($imageId);

        if (str_ends_with($imageId, '.svg')) {
            return $this->getIconOutput($asset, $attributes);
        }

        return $this->getImageTag($asset->getUrl(), $attributes);
    }

    /**
     * @param string $imageUrl
     * @param array $attributes
     * @return string
     * @throws LocalizedException
     * @deprecated Use get() instead
     */
    public function getByUrl(string $imageUrl, array $attributes = []): string
    {
        return $this->get($imageUrl, $attributes);
    }

    public function icon(string $iconId)
    {
        $iconSize = $this->getIconSize($iconId);
        $imageId = $this->iconPrefix.'/'.$this->iconSet.'/'.$iconId.'.svg';

        return $this->get($imageId, [
            'width' => $iconSize,
            'height' => $iconSize,
        ]);
    }

    private function getIconSize(string $iconId): int
    {
        $iconConfiguration = $this->getIconConfiguration();
        if (isset($iconConfiguration[$iconId]['size'])) {
            return $iconConfiguration[$iconId]['size'];
        }

        return $this->iconSize;
    }

    private function getIconConfiguration(): array
    {
        if (false === $this->block instanceof AbstractBlock) {
            return [];
        }

        return (array) $this->block->getIcons();
    }

    private function getImageTag(string $imageUrl, array $attributes = []): string
    {
        $htmlAttributes = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $htmlAttributes[] = $attributeName.'="'.$attributeValue.'"';
        }

        $htmlAttributes = implode(' ', $htmlAttributes);

        return '<img src="'.$imageUrl.'" '.$htmlAttributes.' />';
    }

    private function getIconOutput(AssetFile $asset, array $attributes = []): string
    {
        $sourceFile = $asset->getSourceFile();
        if (false === $sourceFile) {
            return $this->getOutputError('No source file');
        }

        if (false === $this->fileDriver->isFile($sourceFile)) {
            return $this->getOutputError(
                'Source file "'.$sourceFile.'" does not exist'
            );
        }

        if (str_ends_with($sourceFile, '.svg')) {
            $iconPath = str_replace(
                $this->directoryList->getRoot().'/',
                '',
                $sourceFile
            );

            try {
                $svgContents = $this->fileDriver->readFile($iconPath);

                return $this->parseSvgAttributes($svgContents, $attributes);
            } catch (Exception $e) {
                return $this->getOutputError($e->getMessage());
            }
        }

        return $this->getByUrl($asset->getUrl());
    }

    private function parseSvgAttributes(
        string $svgContents,
        array $attributes = []
    ): string {
        $svgElement = new SimpleXMLElement($svgContents);
        $svgElement->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
        $svgElement->registerXPathNamespace('xlink', 'http://www.w3.org/1999/xlink');

        foreach ($attributes as $attributeName => $attributeValue) {
            $svgElement->addAttribute($attributeName, (string)$attributeValue);
        }

        $xmlString = (string)$svgElement->asXML();
        $xmlString = str_replace("<?xml version=\"1.0\"?>\n", '', $xmlString);

        return $xmlString;
    }

    private function getOutputError(string $error): string
    {
        if ($this->appState->getMode() === AppState::MODE_DEVELOPER) {
            return '<!-- '.$error.' -->';
        }

        return '';
    }
}
