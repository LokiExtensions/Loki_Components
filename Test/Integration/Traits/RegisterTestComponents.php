<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Traits;

use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Config\XmlConfig;
use Loki\Components\Config\XmlConfig\Definition\ComponentDefinition;
use Loki\Components\Observer\AddHtmlAttributesToComponentBlock;
use Loki\Components\Observer\AssignAdditionalBlockVariables;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\ObjectManager as TestObjectManager;

/**
 * Replaces the components of loki_components.xml with test components, without touching the module configuration
 */
trait RegisterTestComponents
{
    private array $replacedSharedInstances = [
        XmlConfig::class,
        ComponentRegistry::class,
        AssignAdditionalBlockVariables::class,
        AddHtmlAttributesToComponentBlock::class,
    ];

    protected function createTestComponentDefinition(
        string $name,
        array $arguments = []
    ): ComponentDefinition {
        return new ComponentDefinition(
            $name,
            $arguments['className'] ?? Component::class,
            $arguments['context'] ?? '',
            $arguments['viewModel'] ?? ComponentViewModel::class,
            $arguments['repository'] ?? '',
            $arguments['targets'] ?? [],
            $arguments['validators'] ?? [],
            $arguments['filters'] ?? [],
        );
    }

    /**
     * @param ComponentDefinition[] $componentDefinitions
     */
    protected function registerTestComponents(array $componentDefinitions): void
    {
        $objectManager = $this->getTestObjectManager();
        $originalDefinitions = $objectManager->get(XmlConfig::class)->getComponentDefinitions();

        $xmlConfig = $this->createMock(XmlConfig::class);
        $xmlConfig->method('getComponentDefinitions')->willReturn([...$componentDefinitions, ...$originalDefinitions]);

        $this->removeReplacedSharedInstances();
        $objectManager->addSharedInstance($xmlConfig, XmlConfig::class);
        $objectManager->addSharedInstance(
            $objectManager->create(ComponentRegistry::class, ['xmlConfig' => $xmlConfig]),
            ComponentRegistry::class
        );
    }

    protected function unregisterTestComponents(): void
    {
        $this->removeReplacedSharedInstances();
    }

    private function removeReplacedSharedInstances(): void
    {
        foreach ($this->replacedSharedInstances as $className) {
            $this->getTestObjectManager()->removeSharedInstance($className);
        }
    }

    private function getTestObjectManager(): TestObjectManager
    {
        $objectManager = ObjectManager::getInstance();
        $this->assertInstanceOf(TestObjectManager::class, $objectManager);

        return $objectManager;
    }
}
