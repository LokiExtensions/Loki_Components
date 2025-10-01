<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\File as AssetFile;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RuntimeException;
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

    public function get(string $image, array $attributes = []): string
    {
        if ($this->isAssetId($image)) {
            return $this->getByAssetId($image, $attributes);
        }

        if ($this->fileDriver->isFile($image)) {
            return $this->getByFile($image, $attributes);
        }

        return $this->getImageTag($image, $attributes);
    }

    public function getByAssetId(string $assetId, array $attributes = []): string
    {
        if (!preg_match('/^([A-Z][A-Za-z0-9_]+)::(.+)$/', $assetId)) {
            throw new RuntimeException('Not an asset ID: '.$assetId);
        }

        $asset = $this->assetRepository->createAsset($assetId);

        if (str_ends_with($assetId, '.svg')) {
            return $this->getIconOutput($asset->getSourceFile(), $attributes);
        }

        return $this->getImageTag($asset->getUrl(), $attributes);
    }

    /**
     * @param string $imageUrl
     * @param array  $attributes
     *
     * @return string
     */
    public function getByUrl(string $imageUrl, array $attributes = []): string
    {
        return $this->getImageTag($imageUrl, $attributes);
    }

    public function getByFile(string $imageFile, array $attributes = []): string
    {
        if (str_ends_with($imageFile, '.svg')) {
            return $this->getIconOutput($imageFile, $attributes);
        }

        throw new RuntimeException('Unable to convert file into image URL: '.$imageFile);
    }

    public function icon(AbstractBlock $block, string $iconId)
    {
        $this->block = $block;
        $iconSize = $this->getIconSize($iconId);
        $imageId = $this->iconPrefix
            . '/' . $this->iconSet
            . '/' . $iconId
            . '.svg';

        return $this->getIconOutput($imageId, [
            'width'  => $iconSize,
            'height' => $iconSize,
        ]);
    }

    public function getIconOutput(
        string $sourceFile,
        array $attributes = []
    ): string {
        if (false === $sourceFile) {
            return $this->getOutputError('No source file');
        }

        if (false === $this->fileDriver->isFile($sourceFile)) {
            return $this->getOutputError(
                'Source file "' . $sourceFile . '" does not exist'
            );
        }

        $iconPath = str_replace(
            $this->directoryList->getRoot() . '/',
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

    private function getIconSize(string $iconId): int
    {
        $iconConfiguration = $this->getIconConfiguration();
        if (isset($iconConfiguration[$iconId]['size'])) {
            return (int)$iconConfiguration[$iconId]['size'];
        }

        return $this->iconSize;
    }

    private function getIconConfiguration(): array
    {
        if (false === $this->block instanceof AbstractBlock) {
            return [];
        }

        return (array)$this->block->getIcons();
    }

    private function getImageTag(
        string $imageUrl,
        array $attributes = []
    ): string {
        $htmlAttributes = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $htmlAttributes[] = $attributeName . '="' . $attributeValue . '"';
        }

        $htmlAttributes[] = 'loading="lazy"';
        $htmlAttributes = implode(' ', $htmlAttributes);

        return '<img src="' . $imageUrl . '" ' . $htmlAttributes . ' />';
    }

    private function parseSvgAttributes(
        string $svgContents,
        array $attributes = []
    ): string {
        $svgElement = new SimpleXMLElement($svgContents);
        $svgElement->registerXPathNamespace(
            'svg', 'http://www.w3.org/2000/svg'
        );
        $svgElement->registerXPathNamespace(
            'xlink', 'http://www.w3.org/1999/xlink'
        );

        foreach ($attributes as $attributeName => $attributeValue) {
            $svgElement->addAttribute($attributeName, (string)$attributeValue);
        }

        $svgElement->addAttribute('x-ignore', '');

        $xmlString = (string)$svgElement->asXML();
        $xmlString = str_replace("<?xml version=\"1.0\"?>\n", '', $xmlString);

        return $xmlString;
    }

    private function getOutputError(string $error): string
    {
        if ($this->appState->getMode() === AppState::MODE_DEVELOPER) {
            return '<!-- ' . $error . ' -->';
        }

        return '';
    }

    private function isAssetId(string $assetId): bool
    {
        if (preg_match('/^([A-Z][A-Za-z0-9_]+)::(.+)$/', $assetId)) {
            return true;
        }

        return false;
    }
}
