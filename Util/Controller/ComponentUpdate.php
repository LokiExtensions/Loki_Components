<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Component\Component;

class ComponentUpdate
{
    public function __construct(
        private readonly Component $component,
        private readonly mixed $componentData = null
    ) {
    }

    public function getComponent(): Component
    {
        return $this->component;
    }

    public function getComponentData(): mixed
    {
        return $this->componentData;
    }
}
