<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Setup;

use Loki\Components\Setup\ModuleDependencies;
use Loki\Components\Setup\Recurring;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use PHPUnit\Framework\TestCase;

class RecurringTest extends TestCase
{
    public function testInstallEnablesMissingDependencies(): void
    {
        $moduleDependencies = $this->createMock(ModuleDependencies::class);
        $moduleDependencies->expects($this->once())
            ->method('enableMissingDependencies')
            ->with('Loki_Components');

        $recurring = new Recurring($moduleDependencies);
        $recurring->install(
            $this->createMock(SchemaSetupInterface::class),
            $this->createMock(ModuleContextInterface::class)
        );
    }
}
