<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Component;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Util\Block\GetElementId;
use Loki\Components\Util\CamelCaseConvertor;
use Loki\Components\Util\Component\JsDataProvider;
use Loki\Components\Util\IdConvertor;
use PHPUnit\Framework\TestCase;

class JsDataProviderTest extends TestCase
{
    use BlockMock;

    private const BLOCK_NAME = 'loki-components.test.foo';

    public function testGetComponentData(): void
    {
        $component = $this->getComponent(
            [
                'targets' => ['foo.bar'],
                'js_data' => ['shared' => 'from-block', 'blockOnly' => true],
            ],
            ['foo.bar', 'baz_qux'],
            ['shared' => 'from-view-model', 'viewModelOnly' => true],
        );

        $data = $this->getJsDataProvider()->getComponentData($component);

        $this->assertSame('LokiComponentsTestFoo', $data['id']);
        $this->assertSame('LokiComponent', $data['name']);
        $this->assertSame(['foo-bar', 'baz-qux'], $data['targets']);
        $this->assertSame(self::BLOCK_NAME, $data['blockId']);
        $this->assertSame('loki-components-test-foo', $data['elementId']);
        $this->assertSame(['required'], $data['validators']);
        $this->assertSame(['security'], $data['filters']);
        $this->assertTrue($data['viewModelOnly']);
        $this->assertTrue($data['blockOnly']);
        $this->assertSame('from-block', $data['shared'], 'Block JS data overrules view model JS data');
    }

    public function testGetComponentNameFromBlock(): void
    {
        $component = $this->getComponent(['js_component_name' => 'FromBlock'], [], [], 'FromViewModel');
        $this->assertSame('FromBlock', $this->getJsDataProvider()->getComponentName($component));
    }

    public function testGetComponentNameFromViewModel(): void
    {
        $component = $this->getComponent([], [], [], 'FromViewModel');
        $this->assertSame('FromViewModel', $this->getJsDataProvider()->getComponentName($component));
    }

    public function testGetComponentNameFallsBackToDefault(): void
    {
        $this->assertSame('LokiComponent', $this->getJsDataProvider()->getComponentName($this->getComponent([], [], [], '')));
        $this->assertSame('LokiComponent', $this->getJsDataProvider()->getComponentName($this->getComponent([], [], [], '0')));
    }

    public function testGetComponentId(): void
    {
        $this->assertSame('LokiComponentsTestFoo', $this->getJsDataProvider()->getComponentId($this->getComponent()));
    }

    private function getComponent(
        array $blockData = [],
        array $viewModelTargets = [],
        array $viewModelJsData = [],
        ?string $viewModelJsComponentName = null
    ): ComponentInterface {
        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $viewModel->method('getTargets')->willReturn($viewModelTargets);
        $viewModel->method('getJsData')->willReturn($viewModelJsData);
        $viewModel->method('getJsComponentName')->willReturn($viewModelJsComponentName);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getBlock')->willReturn($this->getBlockMock(self::BLOCK_NAME, $blockData));
        $component->method('getViewModel')->willReturn($viewModel);
        $component->method('getValidators')->willReturn(['required']);
        $component->method('getFilters')->willReturn(['security']);

        return $component;
    }

    private function getJsDataProvider(): JsDataProvider
    {
        $idConvertor = new IdConvertor();
        return new JsDataProvider(new GetElementId($idConvertor), $idConvertor, new CamelCaseConvertor());
    }
}
