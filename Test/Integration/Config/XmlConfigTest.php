<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Config;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Loki\Components\Config\XmlConfig;

class XmlConfigTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     */
    public function testBaseComponentsAreLoadedInFrontendArea(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode(Area::AREA_FRONTEND);

        $definitions = $objectManager->get(XmlConfig::class)->getComponentDefinitions();

        $this->assertArrayHasKey('loki-components.modal', $definitions);
        $this->assertNotEmpty($definitions['loki-components.modal']->getClassName());
    }
}
