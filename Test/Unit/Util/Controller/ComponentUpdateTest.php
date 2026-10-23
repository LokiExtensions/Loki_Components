<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Controller;

use Loki\Components\Component\Component;
use Loki\Components\Util\Controller\ComponentUpdate;
use Magento\Framework\View\Element\AbstractBlock;
use PHPUnit\Framework\TestCase;

class ComponentUpdateTest extends TestCase
{
    public function testGetters(): void
    {
        $block = $this->createMock(AbstractBlock::class);
        $component = $this->createMock(Component::class);

        $componentUpdate = new ComponentUpdate($block, $component, ['foo' => 'bar']);

        $this->assertSame($block, $componentUpdate->getBlock());
        $this->assertSame($component, $componentUpdate->getComponent());
        $this->assertSame(['foo' => 'bar'], $componentUpdate->getComponentData());
    }

    public function testComponentDataDefaultsToNull(): void
    {
        $componentUpdate = new ComponentUpdate(
            $this->createMock(AbstractBlock::class),
            $this->createMock(Component::class)
        );

        $this->assertNull($componentUpdate->getComponentData());
    }
}
