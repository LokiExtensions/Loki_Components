<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Component;

use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentContext;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Exception\NoComponentFoundException;
use Loki\Components\Test\Integration\Traits\GetComponent;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class ComponentRegistryTest extends TestCase
{
    use GetComponent;

    #[DataProvider('getComponentNames')]
    public function testComponentsOfModuleAreRegistered(string $componentName): void
    {
        $component = $this->getComponentRegistry()->getComponentByName($componentName);

        $this->assertInstanceOf(Component::class, $component);
        $this->assertSame($componentName, $component->getName());
        $this->assertInstanceOf(ComponentContext::class, $component->getContext());
        $this->assertInstanceOf(ComponentViewModel::class, $component->getViewModel());
        $this->assertNull($component->getRepository());
        $this->assertSame($component, $this->getComponentRegistry()->getComponentByName($componentName));
    }

    public static function getComponentNames(): array
    {
        return [
            ['loki-components.modal'],
            ['loki-components.test.dummy1'],
            ['loki-components.test.dummy2'],
            ['loki-components.test.dummy-html'],
        ];
    }

    public function testUnknownComponentThrowsException(): void
    {
        $this->expectException(NoComponentFoundException::class);
        $this->getComponentRegistry()->getComponentByName('loki-components.test.unknown');
    }

    public function testGetBlockNameFromElementId(): void
    {
        $this->assertSame(
            'loki-components.test.dummy-html',
            $this->getComponentRegistry()->getBlockNameFromElementId('loki-components-test-dummy-html')
        );
    }

    #[AppIsolation(true)]
    public function testComponentIsConnectedToBlockOfLayout(): void
    {
        $component = $this->getComponentWithBlock('loki-components.test.dummy-html', ['loki_components_index_test']);

        $block = $component->getBlock();
        $this->assertInstanceOf(AbstractBlock::class, $block);
        $this->assertSame('loki-components.test.dummy-html', $block->getNameInLayout());
        $this->assertArrayHasKey('html', $component->getViewModel()->getJsData());
    }

    private function getComponentRegistry(): ComponentRegistry
    {
        return ObjectManager::getInstance()->get(ComponentRegistry::class);
    }
}
