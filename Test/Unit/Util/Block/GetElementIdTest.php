<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Block;

use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Util\Block\GetElementId;
use Loki\Components\Util\IdConvertor;
use PHPUnit\Framework\TestCase;

class GetElementIdTest extends TestCase
{
    use BlockMock;

    public function testUsesNameInLayout(): void
    {
        $block = $this->getBlockMock('Loki-Components.Test_Block');
        $this->assertSame('loki-components-test-block', $this->getGetElementId()->execute($block));
    }

    public function testUniqIdOverrulesNameInLayout(): void
    {
        $block = $this->getBlockMock('loki-components.test', ['uniq_id' => 'Custom.Unique_Id']);
        $this->assertSame('custom-unique-id', $this->getGetElementId()->execute($block));
    }

    private function getGetElementId(): GetElementId
    {
        return new GetElementId(new IdConvertor());
    }
}
