<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\ComponentsDir;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Magento\Framework\Module\Status as ModuleStatus;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Module\ModuleList\Loader as ModuleListLoader;

#[AppArea('frontend')]
#[ComponentsDir('../../../../vendor/loki/magento2-components/Test/Integration/TestModules')]
class TargetRendererWithCustomPageLayoutTest extends AbstractTargetRendererTestCase
{
    use AssertModuleIsRegistered;
    use AssertModuleIsEnabled;

    #[AppIsolation(true)]
    public function testCustomPageLayout(): void
    {
        //$this->enableModule('LokiTest_CustomPageLayout');

        $handles = ['default'];
        $pageHandles = ['loki_test'];
        $blockName = 'loki-test-block';
        $layout = $this->getLayoutLoader()->load($handles, $pageHandles, false);

        $allBlockNames = array_keys($layout->getAllBlocks());
        $this->assertNotEmpty($allBlockNames);

        $debugMsg = 'Blocks: '.implode(', ', $allBlockNames);
        $this->assertContains($blockName, $allBlockNames, $debugMsg);

        $htmlParts = $this->getTargetRenderer()->render($layout, [$blockName], false);
        $this->assertNotEmpty($htmlParts);
    }

    private function enableModule(string $moduleName = ''): void
    {
        $this->assertModuleIsRegistered($moduleName);

        $objectManager = Bootstrap::getObjectManager();
        $moduleStatus = $objectManager->get(ModuleStatus::class);

        $moduleStatus->setIsEnabled(true, [
            $moduleName,
        ]);

        Bootstrap::getInstance()->reinitialize();

        $this->assertModuleIsEnabled($moduleName);
        $this->assertModuleIsEnabled('Magento_Theme');
    }
}
