<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Observer;

use Loki\Components\Observer\AddDomIdToBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager as TestObjectManager;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class AddDomIdToBlockTest extends TestCase
{
    private const BLOCK_NAME = 'loki-test.dom-id';

    protected function setUp(): void
    {
        $objectManager = $this->getTestObjectManager();
        $objectManager->removeSharedInstance(AddDomIdToBlock::class);
        $objectManager->addSharedInstance(
            $objectManager->create(AddDomIdToBlock::class, ['blockNames' => [self::BLOCK_NAME]]),
            AddDomIdToBlock::class
        );
    }

    protected function tearDown(): void
    {
        $this->getTestObjectManager()->removeSharedInstance(AddDomIdToBlock::class);
    }

    public function testAddsDomIdToConfiguredBlock(): void
    {
        $html = $this->createBlock(self::BLOCK_NAME)->toHtml();

        $this->assertStringStartsWith('<div id="loki-test-dom-id" class="loki-components-test-dummy">', trim($html));
    }

    public function testAddsDomIdBasedOnUniqId(): void
    {
        $block = $this->createBlock(self::BLOCK_NAME);
        $block->setUniqId('custom-uniq');

        $this->assertStringStartsWith('<div id="custom-uniq" ', trim($block->toHtml()));
    }

    public function testLeavesOtherBlocksAlone(): void
    {
        $html = $this->createBlock('loki-test.other')->toHtml();

        $this->assertStringStartsWith('<div class="loki-components-test-dummy">', trim($html));
        $this->assertStringNotContainsString(' id="', $html);
    }

    private function createBlock(string $name): Template
    {
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, $name, [
            'data' => ['template' => 'Loki_Components::test/dummy.phtml'],
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
