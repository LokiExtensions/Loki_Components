<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Component;

use Loki\Components\Component\ComponentContextInterface;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRepository;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Components\Messages\LocalMessageRegistry;
use PHPUnit\Framework\TestCase;

class ComponentRepositoryTest extends TestCase
{
    public function testDefaults(): void
    {
        $repository = $this->getRepository();
        $this->assertNull($repository->getDefaultValue());
        $this->assertSame(0, $repository->getPriority());
    }

    public function testDelegatesToComponent(): void
    {
        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $context = $this->createMock(ComponentContextInterface::class);
        $globalMessageRegistry = $this->createMock(GlobalMessageRegistry::class);
        $localMessageRegistry = new LocalMessageRegistry();

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getName')->willReturn('foo.bar');
        $component->method('getViewModel')->willReturn($viewModel);
        $component->method('getContext')->willReturn($context);
        $component->method('getGlobalMessageRegistry')->willReturn($globalMessageRegistry);
        $component->method('getLocalMessageRegistry')->willReturn($localMessageRegistry);

        $repository = $this->getRepository();
        $repository->setComponent($component);

        $this->assertSame($component, $repository->getComponent());
        $this->assertSame('foo.bar', $repository->getComponentName());
        $this->assertSame($viewModel, $repository->getViewModel());
        $this->assertSame($context, $repository->getContextPublic());
        $this->assertSame($globalMessageRegistry, $repository->getGlobalMessageRegistry());
        $this->assertSame($localMessageRegistry, $repository->getLocalMessageRegistry());
    }

    private function getRepository(): ComponentRepository
    {
        return new class extends ComponentRepository {
            public function getValue(): mixed
            {
                return null;
            }

            public function saveValue(mixed $value): void
            {
            }

            public function getContextPublic(): ComponentContextInterface
            {
                return $this->getContext();
            }
        };
    }
}
