<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Factory;

use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Factory\ViewModelFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

class ViewModelFactoryTest extends TestCase
{
    public function testGetReturnsSharedViewModel(): void
    {
        $viewModel = $this->createMock(ArgumentInterface::class);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())->method('get')->with('Foo\\ViewModel')->willReturn($viewModel);

        $this->assertSame($viewModel, (new ViewModelFactory($objectManager))->get('Foo\\ViewModel'));
    }

    public function testCreatePassesData(): void
    {
        $viewModel = $this->createMock(ArgumentInterface::class);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('create')
            ->with('Foo\\ViewModel', ['foo' => 'bar'])
            ->willReturn($viewModel);

        $this->assertSame($viewModel, (new ViewModelFactory($objectManager))->create('Foo\\ViewModel', ['foo' => 'bar']));
    }

    #[DataProvider('getInvalidViewModels')]
    public function testRejectsInvalidViewModels(string $type, string $expectedMessage): void
    {
        $viewModel = match ($type) {
            'empty' => null,
            'no-argument-interface' => new stdClass(),
            'component-view-model' => $this->createMock(ComponentViewModelInterface::class),
        };

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('get')->willReturn($viewModel);
        $objectManager->method('create')->willReturn($viewModel);

        $viewModelFactory = new ViewModelFactory($objectManager);

        try {
            $viewModelFactory->get('Foo\\ViewModel');
            $this->fail('No exception thrown for get()');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString($expectedMessage, $exception->getMessage());
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessage);
        $viewModelFactory->create('Foo\\ViewModel');
    }

    public static function getInvalidViewModels(): array
    {
        return [
            ['empty', 'could not be instantiated'],
            ['no-argument-interface', 'is not a ViewModel'],
            ['component-view-model', 'can not be a ComponentViewModel'],
        ];
    }
}
