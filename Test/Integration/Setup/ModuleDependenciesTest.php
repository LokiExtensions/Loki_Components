<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Setup;

use Loki\Components\Setup\ModuleDependencies;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\Manager as ModuleManager;
use PHPUnit\Framework\TestCase;

class ModuleDependenciesTest extends TestCase
{
    public function testHasNoMissingDependencies(): void
    {
        $this->assertSame([], $this->getModuleDependencies()->getMissingDependencies('Loki_Components'));
    }

    public function testReportsDisabledSequenceModules(): void
    {
        $moduleManager = $this->createMock(ModuleManager::class);
        $moduleManager->method('isEnabled')->willReturnCallback(fn (string $moduleName) => $moduleName !== 'Loki_CssUtils');

        $moduleDependencies = ObjectManager::getInstance()->create(ModuleDependencies::class, [
            'moduleManager' => $moduleManager,
        ]);

        $this->assertSame(['Loki_CssUtils'], $moduleDependencies->getMissingDependencies('Loki_Components'));
    }

    private function getModuleDependencies(): ModuleDependencies
    {
        return ObjectManager::getInstance()->get(ModuleDependencies::class);
    }
}
