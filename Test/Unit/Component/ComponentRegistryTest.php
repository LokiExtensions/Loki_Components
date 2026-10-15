<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Component;

use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentFactory;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Config\XmlConfig;
use Loki\Components\Config\XmlConfig\Definition\ComponentDefinition;
use Loki\Components\Exception\NoComponentFoundException;
use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Util\IdConvertor;
use PHPUnit\Framework\TestCase;

class ComponentRegistryTest extends TestCase
{
    use BlockMock;

    public function testGetComponentByNameCreatesComponentOnce(): void
    {
        $component = $this->createMock(Component::class);

        $componentFactory = $this->createMock(ComponentFactory::class);
        $componentFactory->expects($this->once())
            ->method('createFromDefinition')
            ->with($this->callback(fn (ComponentDefinition $definition) => $definition->getName() === 'foo.bar'))
            ->willReturn($component);

        $registry = $this->getRegistry($componentFactory);
        $this->assertSame($component, $registry->getComponentByName('foo.bar'));
        $this->assertSame($component, $registry->getComponentByName('foo.bar'));
    }

    public function testGetComponentByNameThrowsExceptionForUnknownComponent(): void
    {
        $this->expectException(NoComponentFoundException::class);
        $this->getRegistry()->getComponentByName('unknown');
    }

    public function testGetComponentFromBlockSetsBlock(): void
    {
        $block = $this->getBlockMock('foo.bar');

        $component = $this->createMock(Component::class);
        $component->expects($this->once())->method('setBlock')->with($block);

        $componentFactory = $this->createMock(ComponentFactory::class);
        $componentFactory->method('createFromDefinition')->willReturn($component);

        $this->assertSame($component, $this->getRegistry($componentFactory)->getComponentFromBlock($block));
    }

    public function testGetComponentFromBlockWithoutNameThrowsException(): void
    {
        $this->expectException(NoComponentFoundException::class);
        $this->getRegistry()->getComponentFromBlock($this->getBlockMock(''));
    }

    public function testGetBlockNameFromElementId(): void
    {
        $registry = $this->getRegistry();

        $this->assertSame('foo.bar', $registry->getBlockNameFromElementId('foo-bar'));
        $this->assertSame('baz.target', $registry->getBlockNameFromElementId('baz-target'));
        $this->assertSame('literal-target', $registry->getBlockNameFromElementId('literal-target'));
    }

    public function testGetBlockNameFromUnknownElementIdThrowsException(): void
    {
        $this->expectException(NoComponentFoundException::class);
        $this->getRegistry()->getBlockNameFromElementId('unknown');
    }

    private function getRegistry(?ComponentFactory $componentFactory = null): ComponentRegistry
    {
        $xmlConfig = $this->createMock(XmlConfig::class);
        $xmlConfig->method('getComponentDefinitions')->willReturn([
            new ComponentDefinition('foo.bar', Component::class, '', '', '', ['baz.target', 'literal-target']),
            new ComponentDefinition('other', Component::class),
        ]);

        return new ComponentRegistry(
            $xmlConfig,
            $componentFactory ?? $this->createMock(ComponentFactory::class),
            new IdConvertor()
        );
    }
}
