<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Magento\Framework\ObjectManagerInterface;

class ComponentUpdateFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []):  ComponentUpdate
    {
        return $this->objectManager->create(ComponentUpdate::class, $data);
    }
}
