<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Setup;

use Loki\Components\Setup\ModuleDependencies;
use Loki\Components\Setup\Recurring;
use Magento\Framework\Module\Status as ModuleStatus;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class RecurringTest extends TestCase
{
    public function testInstallEnablesNothingWhenAllDependenciesAreEnabled(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $moduleStatus = $this->createMock(ModuleStatus::class);
        $moduleStatus->expects($this->once())
            ->method('setIsEnabled')
            ->with(true, []);

        $moduleDependencies = $objectManager->create(ModuleDependencies::class, ['moduleStatus' => $moduleStatus]);
        $recurring = $objectManager->create(Recurring::class, ['moduleDependencies' => $moduleDependencies]);

        $recurring->install(
            $this->createMock(SchemaSetupInterface::class),
            $this->createMock(ModuleContextInterface::class)
        );
    }
}
