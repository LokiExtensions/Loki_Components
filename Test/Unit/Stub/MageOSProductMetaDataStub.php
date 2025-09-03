<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Magento\Framework\App\ProductMetadataInterface;

class MageOSProductMetaDataStub implements ProductMetadataInterface
{
    public function getDistributionName(): string
    {
        return 'Mage-OS';
    }

    public function getVersion()
    {
        return '1.3.0';
    }

    public function getEdition()
    {
        return 'Community';
    }

    public function getName()
    {
        return 'Magento';
    }
}
