<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Component;

use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentContext;
use Loki\Components\Component\ComponentContextInterface;
use Loki\Components\Component\ComponentFactory;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Config\XmlConfig\Definition\ComponentDefinition;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class ComponentFactoryTest extends TestCase
{
    public function testCreateFromDefinitionWithDefaultContext(): void
    {
        $context = $this->createMock(ComponentContextInterface::class);
        $component = $this->createMock(Component::class);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())->method('get')->with(ComponentContext::class)->willReturn($context);
        $objectManager->expects($this->once())
            ->method('create')
            ->with(Component::class, [
                'name' => 'foo.bar',
                'context' => $context,
                'targets' => ['foo.target'],
                'viewModelClass' => ComponentViewModel::class,
                'repositoryClass' => null,
                'validators' => ['required'],
                'filters' => ['trim'],
            ])
            ->willReturn($component);

        $definition = new ComponentDefinition(
            'foo.bar',
            Component::class,
            '',
            ComponentViewModel::class,
            '',
            ['foo.target'],
            ['required'],
            ['trim']
        );

        $this->assertSame($component, (new ComponentFactory($objectManager))->createFromDefinition($definition));
    }

    public function testCreateFromDefinitionWithCustomContext(): void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('get')
            ->with('Loki\\Components\\Test\\CustomContext')
            ->willReturn($this->createMock(ComponentContextInterface::class));
        $objectManager->method('create')->willReturn($this->createMock(Component::class));

        $definition = new ComponentDefinition('foo.bar', Component::class, 'Loki\\Components\\Test\\CustomContext');
        (new ComponentFactory($objectManager))->createFromDefinition($definition);
    }

    public function testCreateFromDefinitionWrapsUnexpectedValueException(): void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('get')->willReturn($this->createMock(ComponentContextInterface::class));
        $objectManager->method('create')->willThrowException(new UnexpectedValueException('Original error'));

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessageMatches('/Failed to create component: .*foo\.bar.*Original error/s');

        (new ComponentFactory($objectManager))->createFromDefinition(new ComponentDefinition('foo.bar', Component::class));
    }
}
