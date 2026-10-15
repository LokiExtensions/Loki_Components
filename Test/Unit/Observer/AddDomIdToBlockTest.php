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

    public function testAddsIdToFirstElement(): void
    {
        $html = $this->execute(self::BLOCK_NAME, "\n  <div\n    class=\"foo\">bar</div>\n");
        $this->assertSame("<div id=\"loki-test-block\"\n    class=\"foo\">bar</div>", $html);
    }

    #[DataProvider('getUntouchedHtml')]
    public function testLeavesHtmlUntouched(string $blockName, string $html): void
    {
        $this->assertSame($html, $this->execute($blockName, $html));
    }

    public static function getUntouchedHtml(): array
    {
        return [
            'block not configured' => ['other.block', "<div\n class=\"foo\">bar</div>"],
            'empty html' => [self::BLOCK_NAME, ''],
            'no element' => [self::BLOCK_NAME, 'plain text'],
            'single character element' => [self::BLOCK_NAME, "<p\n class=\"foo\">bar</p>"],
        ];
    }

    public function testIgnoresNonTemplateBlocks(): void
    {
        $block = $this->getMockBuilder(AbstractBlock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getNameInLayout'])
            ->getMock();
        $block->method('getNameInLayout')->willReturn(self::BLOCK_NAME);

        $html = "<div\n class=\"foo\">bar</div>";
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
