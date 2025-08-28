<?php
declare(strict_types=1);

namespace Loki\Components\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Recurring implements InstallSchemaInterface
{
    public function __construct(
        private ModuleDependencies $moduleDependencies
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->moduleDependencies->enableMissingDependencies('Loki_Components');
    }
}
