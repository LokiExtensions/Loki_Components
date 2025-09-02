<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\File as AssetFile;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;

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

    public function get(string $imageId): string
    {
        $asset = $this->assetRepository->createAsset($imageId);

        return $this->getIconOutput($asset);
    }

    public function getByUrl(string $imageUrl): string
    {
        return '<img src="'.$imageUrl.'" />';
    }

    private function getIconOutput(AssetFile $asset): string
    {
        $sourceFile = $asset->getSourceFile();
        if (false === $sourceFile) {
            return $this->getOutputError('No source file');
        }

        if (false === $this->fileDriver->isFile($sourceFile)) {
            return $this->getOutputError('Source file "'.$sourceFile.'" does not exist');
        }

        if (str_ends_with($sourceFile, '.svg')) {
            $iconPath = str_replace($this->directoryList->getRoot().'/', '', $sourceFile);
            try {
                return $this->fileDriver->readFile($iconPath);
            } catch (Exception $e) {
                return $this->getOutputError($e->getMessage());
            }
        }

        return $this->getByUrl($asset->getUrl());
    }

    private function getOutputError(string $error): string
    {
        if ($this->appState->getMode() === AppState::MODE_DEVELOPER) {
            return '<!-- '.$error.' -->';
        }

        return '';
    }
}
