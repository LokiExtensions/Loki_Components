<?php
declare(strict_types=1);

namespace Loki\Components\Setup;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Module\Status as ModuleStatus;

class ModuleDependencies
{
    public function __construct(
        private ComponentRegistrar $componentRegistrar,
        private ModuleManager $moduleManager,
        private ModuleStatus $moduleStatus,
    ) {
    }

    public function getMissingDependencies(string $moduleName): bool|array
    {
        $modulePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
        $moduleXmlPath = $modulePath.'/etc/module.xml';
        $rootNode = simplexml_load_file($moduleXmlPath);

        $moduleSequence = [];
        if ($rootNode->module->sequence) {
            foreach ($rootNode->module->sequence->module as $sequenceModule) {
                $moduleSequence[] = (string)$sequenceModule['name'];
            }
        }


        $missingModules = [];
        foreach ($moduleSequence as $requiredModule) {
            if (!$this->moduleManager->isEnabled($requiredModule)) {
                $missingModules[] = $requiredModule;
            }
        }

        return $missingModules;
    }

    public function enableMissingDependencies(string $moduleName): void
    {
        $missingDependencies = $this->getMissingDependencies($moduleName);
        $this->moduleStatus->setIsEnabled(true, $missingDependencies);
    }
}
