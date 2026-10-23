<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Plugin;

use Loki\Components\Plugin\AddCspInlineScripts;
use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager as TestObjectManager;
use PHPUnit\Framework\TestCase;
use Yireo\CspUtilities\Util\ReplaceInlineScripts;

#[AppArea('frontend')]
#[AppIsolation(true)]
class AddCspInlineScriptsTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->getTestObjectManager()->removeSharedInstance(AddCspInlineScripts::class);
    }

    public function testPluginIsRegisteredOnTemplate(): void
    {
        $pluginList = Bootstrap::getObjectManager()->get(PluginListInterface::class);
        $pluginInfo = $pluginList->get(Template::class, []);

        $this->assertArrayHasKey('Loki_Components::addCspInlineScripts', $pluginInfo);
        $this->assertSame(AddCspInlineScripts::class, $pluginInfo['Loki_Components::addCspInlineScripts']['instance']);
    }

    public function testInlineScriptsOfLokiComponentsBlocksAreReplaced(): void
    {
        $this->replacePlugin(true);

        $html = $this->createBlock('loki-components.script.local-storage')->toHtml();

        $this->assertStringContainsString('<!-- replaced -->', $html);
        $this->assertStringContainsString('LokiComponentsStorage', $html);
    }

    public function testOtherBlocksAreLeftAlone(): void
    {
        $this->replacePlugin(false);

        $html = $this->createBlock('loki-test.script.local-storage')->toHtml();

        $this->assertStringNotContainsString('<!-- replaced -->', $html);
        $this->assertStringContainsString('LokiComponentsStorage', $html);
    }

    private function replacePlugin(bool $expectReplace): void
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($expectReplace ? $this->atLeastOnce() : $this->never())
            ->method('replace')
            ->willReturnCallback(fn (string $html) => '<!-- replaced -->' . $html);

        $objectManager = $this->getTestObjectManager();
        $objectManager->removeSharedInstance(AddCspInlineScripts::class);
        $objectManager->addSharedInstance(
            new AddCspInlineScripts($replaceInlineScripts),
            AddCspInlineScripts::class
        );
    }

    private function createBlock(string $name): Template
    {
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, $name, [
            'data' => ['template' => 'Loki_Components::script/local-storage.phtml'],
        ]);
        $this->assertInstanceOf(Template::class, $block);

        return $block;
    }

    private function getTestObjectManager(): TestObjectManager
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->assertInstanceOf(TestObjectManager::class, $objectManager);

        return $objectManager;
    }
}
