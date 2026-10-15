<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Template;

use Loki\Components\Util\Controller\LayoutLoader;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class PollingTemplateTest extends TestCase
{
    private const BLOCK_NAME = 'loki-components.script.polling';

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/polling_interval', 1500, 'store', 'default')]
    public function testRendersPollingScriptWithConfiguredInterval(): void
    {
        $html = $this->renderPollingBlock();

        $this->assertStringContainsString('loki_components/index/polling', $html);
        $this->assertStringContainsString('}, 1500);', $html);
    }

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/polling_interval', 1500, 'store', 'default')]
    public function testBlockArgumentOverrulesConfiguredInterval(): void
    {
        $html = $this->renderPollingBlock(['polling_interval' => 2500]);
        $this->assertStringContainsString('}, 2500);', $html);
    }

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/polling_interval', 0, 'store', 'default')]
    public function testDoesNotPollWithoutInterval(): void
    {
        $this->assertStringNotContainsString('loki_components/index/polling', $this->renderPollingBlock());
    }

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/polling_interval', 100, 'store', 'default')]
    public function testDoesNotPollWithTooShortInterval(): void
    {
        $this->assertStringNotContainsString('loki_components/index/polling', $this->renderPollingBlock());
    }

    private function renderPollingBlock(array $data = []): string
    {
        $layout = ObjectManager::getInstance()->get(LayoutLoader::class)->load(['loki_components']);
        $block = $layout->getBlock(self::BLOCK_NAME);
        $this->assertInstanceOf(AbstractBlock::class, $block);
        $block->addData($data);

        return (string)$block->toHtml();
    }
}
