<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Plugin;

use Loki\Components\Plugin\AddCspInlineScripts;
use Loki\Components\Test\Unit\Mock\BlockMock;
use PHPUnit\Framework\TestCase;
use Yireo\CspUtilities\Util\ReplaceInlineScripts;

class AddCspInlineScriptsTest extends TestCase
{
    use BlockMock;

    public function testReplacesScriptsOfLokiComponentsBlocks(): void
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($this->once())
            ->method('replace')
            ->with('<script>foo</script>')
            ->willReturn('<script nonce="abc">foo</script>');

        $plugin = new AddCspInlineScripts($replaceInlineScripts);
        $this->assertSame(
            '<script nonce="abc">foo</script>',
            $plugin->afterToHtml($this->getBlockMock('loki-components.script.foo'), '<script>foo</script>')
        );
    }

    public function testLeavesOtherBlocksAlone(): void
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($this->never())->method('replace');

        $plugin = new AddCspInlineScripts($replaceInlineScripts);
        $this->assertSame('<script>foo</script>', $plugin->afterToHtml($this->getBlockMock('checkout.foo'), '<script>foo</script>'));
        $this->assertSame('', $plugin->afterToHtml($this->getBlockMock('checkout.foo'), null));
    }
}
