<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Yireo\LokiComponents\Component\Component;
use Yireo\LokiComponents\Component\ComponentContext;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentViewModel;

trait CreateComponent
{
    protected function createComponent(string $componentClass = '', array $arguments = []): ComponentInterface
    {
        if (empty($componentClass)) {
            $componentClass = Component::class;
        }

        $objectManager = ObjectManager::getInstance();

        if (!isset($arguments['name'])) {
            $arguments['name'] = 'example';
        }

        if (!isset($arguments['viewModelClass'])) {
            $arguments['viewModelClass'] = ComponentViewModel::class;
        }

        if (!isset($arguments['context'])) {
            $arguments['context'] = $objectManager->get(ComponentContext::class);
        }

        return $objectManager->create($componentClass, $arguments);
    }
}
