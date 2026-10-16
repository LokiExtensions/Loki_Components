<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Observer;

use Loki\Components\Observer\AddDomIdToBlock;
use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Util\Block\GetElementId;
use Loki\Components\Util\IdConvertor;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Element\AbstractBlock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AddDomIdToBlockTest extends TestCase
{
    use BlockMock;

    private const BLOCK_NAME = 'loki.test.block';

    #[DataProvider('getHtmlWithAddedId')]
    public function testAddsIdToFirstElement(string $html, string $expectedHtml): void
    {
        $this->assertSame($expectedHtml, $this->execute(self::BLOCK_NAME, $html));
    }

    public static function getHtmlWithAddedId(): array
    {
        return [
            'element without attributes' => [
                '<section>bar</section>',
                '<section id="loki-test-block">bar</section>',
            ],
            'element with attributes' => [
                '<div class="foo"><span id="inner">bar</span></div>',
                '<div id="loki-test-block" class="foo"><span id="inner">bar</span></div>',
            ],
            'attributes on multiple lines' => [
                "\n  <div\n    class=\"foo\">bar</div>\n",
                "<div id=\"loki-test-block\"\n    class=\"foo\">bar</div>",
            ],
            'attribute ending with id' => [
                '<div data-id="foo">bar</div>',
                '<div id="loki-test-block" data-id="foo">bar</div>',
            ],
            'single letter element' => [
                '<p class="foo">bar</p>',
                '<p id="loki-test-block" class="foo">bar</p>',
            ],
            'heading element' => [
                '<h2>bar</h2>',
                '<h2 id="loki-test-block">bar</h2>',
            ],
            'html with regex characters' => [
                '<div x-bind:class="{ \'active\': open }" data-pattern="(a|b)+$">bar</div>',
                '<div id="loki-test-block" x-bind:class="{ \'active\': open }" data-pattern="(a|b)+$">bar</div>',
            ],
        ];
    }

    #[DataProvider('getUntouchedHtml')]
    public function testLeavesHtmlUntouched(string $blockName, string $html): void
    {
        $this->assertSame($html, $this->execute($blockName, $html));
    }

    public static function getUntouchedHtml(): array
    {
        return [
            'block not configured' => ['other.block', '<div class="foo">bar</div>'],
            'empty html' => [self::BLOCK_NAME, ''],
            'existing id' => [self::BLOCK_NAME, '<div class="foo" id="custom">bar</div>'],
            'existing id on another line' => [self::BLOCK_NAME, "<div\n class=\"foo\"\n id=\"custom\">bar</div>"],
            'no element' => [self::BLOCK_NAME, 'plain text'],
            'comment' => [self::BLOCK_NAME, '<!-- comment --><div>bar</div>'],
        ];
    }

    public function testIgnoresNonTemplateBlocks(): void
    {
        $block = $this->getMockBuilder(AbstractBlock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getNameInLayout'])
            ->getMock();
        $block->method('getNameInLayout')->willReturn(self::BLOCK_NAME);

        $html = '<div class="foo">bar</div>';
        $transport = new DataObject(['html' => $html]);
        $this->getObserver()->execute($this->getEventObserver($block, $transport));
        $this->assertSame($html, $transport->getHtml());
    }

    private function execute(string $blockName, string $html): string
    {
        $transport = new DataObject(['html' => $html]);
        $this->getObserver()->execute($this->getEventObserver($this->getBlockMock($blockName), $transport));

        return (string)$transport->getHtml();
    }

    private function getObserver(): AddDomIdToBlock
    {
        return new AddDomIdToBlock(new GetElementId(new IdConvertor()), [self::BLOCK_NAME]);
    }

    private function getEventObserver(AbstractBlock $block, DataObject $transport): Observer
    {
        return new Observer(['event' => new Event(['block' => $block, 'transport' => $transport])]);
    }
}
