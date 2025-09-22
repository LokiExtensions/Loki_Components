<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\File as AssetFile;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SimpleXMLElement;

class ImageOutput implements ArgumentInterface
{
    private ?Filesystem\Directory\ReadInterface $fileDriver = null;

    public function __construct(
        Filesystem $filesystem,
        private readonly AssetRepository $assetRepository,
        private readonly DirectoryList $directoryList,
        private readonly AppState $appState,
    ) {
        $this->fileDriver = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    public function get(string $imageId, array $attributes = []): string
    {
        // @todo: Allow inline or as image
        $asset = $this->assetRepository->createAsset($imageId);

        return $this->getIconOutput($asset, $attributes);
    }

    public function getByUrl(string $imageUrl, array $attributes = []): string
    {
        $htmlAttributes = [];
        foreach ($htmlAttributes as $htmlAttributeName => $htmlAttributeValue) {
            $htmlAttributes[] = $htmlAttributeName . '="' . $htmlAttributeValue
                . '"';
        }

        $htmlAttributes = implode(' ', $htmlAttributes);
        return '<img src="' . $imageUrl . '" ' . $htmlAttributes . ' />';
    }

    private function getIconOutput(AssetFile $asset, array $attributes = []): string
    {
        $sourceFile = $asset->getSourceFile();
        if (false === $sourceFile) {
            return $this->getOutputError('No source file');
        }

        if (false === $this->fileDriver->isFile($sourceFile)) {
            return $this->getOutputError(
                'Source file "' . $sourceFile . '" does not exist'
            );
        }

        if (str_ends_with($sourceFile, '.svg')) {
            $iconPath = str_replace(
                $this->directoryList->getRoot() . '/', '', $sourceFile
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

        foreach ($attributes as $attributeName => $attributeValue) {
            $svgElement->addAttribute($attributeName, $attributeValue);
        }

        return $svgContents;
    }

    private function getOutputError(string $error): string
    {
        if ($this->appState->getMode() === AppState::MODE_DEVELOPER) {
            return '<!-- ' . $error . ' -->';
        }

        return '';
    }
}
