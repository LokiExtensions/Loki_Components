<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Components\Util\Controller\TargetRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;

#[AppArea('frontend')]
class TargetRendererTest extends TestCase
{
    use AssertModuleIsRegistered;
    use AssertModuleIsEnabled;

    #[DataProvider('getTestData')]
    #[AppIsolation(true)]
    public function testRenderWithoutIsolation(string $blockName, array $handles, array $pageHandles): void
    {
        $layout = $this->getLayoutLoader()->load($handles, $pageHandles);

        $allBlockNames = array_keys($layout->getAllBlocks());
        $this->assertNotEmpty($allBlockNames);

        $debugMsg = 'Blocks: '.implode(', ', $allBlockNames);
        $this->assertContains($blockName, $allBlockNames, $debugMsg);

        $htmlParts = $this->getTargetRenderer()->render($layout, [$blockName]);
        $this->assertNotEmpty($htmlParts);
    }

    #[DataProvider('getTestData')]
    #[AppIsolation(true)]
    public function testRenderWithIsolation(string $blockName, array $handles, array $pageHandles): void
    {
        $layout = $this->getLayoutLoader()->load($handles, $pageHandles, true);

        $allBlockNames = array_keys($layout->getAllBlocks());
        $this->assertNotEmpty($allBlockNames);

        $debugMsg = 'Blocks: '.implode(', ', $allBlockNames);
        $this->assertContains($blockName, $allBlockNames, $debugMsg);

        $htmlParts = $this->getTargetRenderer()->render($layout, [$blockName], true);
        $this->assertNotEmpty($htmlParts);
    }

    public static function getTestData(): array
    {
        return [
            ['loki-components.utils.local-messages', ['loki_messages'], []],
            ['loki.messages', ['default', 'loki_messages'], []],
            ['footer_links', ['default'], []],
        ];
    }

    private function getLayoutLoader(): LayoutLoader
    {
        return ObjectManager::getInstance()->get(LayoutLoader::class);
    }

    private function getTargetRenderer(): TargetRenderer
    {
        return ObjectManager::getInstance()->get(TargetRenderer::class);
    }
}
