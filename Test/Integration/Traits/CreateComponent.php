<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentContext;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModel;

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
