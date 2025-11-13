<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Component\ComponentInterface;
use Magento\Framework\Event\Manager as EventManager;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Validator\Validator;

class RepositoryDispatcher
{
    public function __construct(
        private readonly EventManager $eventManager,
        private readonly Filter $filter,
        private readonly Validator $validator,
    ) {
    }

    public function dispatch(ComponentInterface $component, mixed $componentData): void
    {
        $this->eventManager->dispatch('loki_components_repository_dispatch', ['component' => $component]);

        $repository = $component->getRepository();
        if (false === $repository instanceof ComponentRepositoryInterface) {
            return;
        }

        $componentData = $this->filter->filter($component, $componentData);
        if (false === $this->validator->validate($component, $componentData)) {
            return;
        }

        $repository->saveValue($componentData);
    }
}
