<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Magento\Framework\App\ProductMetadataInterface;

class MagentoVersion
{
    public function __construct(
        private readonly ProductMetadataInterface $productMetadata,
    ) {
    }

    public function isMagentoVersion(string $requestedVersion, string $operator = 'eq'): bool
    {
        if ($this->isMageOS()) {
            return false;
        }

        return version_compare($requestedVersion, $this->productMetadata->getVersion(), $operator);
    }

    public function isMageOSVersion(string $requestedVersion, string $operator = 'eq'): bool
    {
        if (false === $this->isMageOS()) {
            return false;
        }

        return version_compare($requestedVersion, $this->productMetadata->getVersion(), $operator);
    }

    public function isMageOS(): bool
    {
        if (false === method_exists($this->productMetadata, 'getDistributionName')) {
            return false;
        }

        return $this->productMetadata->getDistributionName() === 'Mage-OS';
    }
}
