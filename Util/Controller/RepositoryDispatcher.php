<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util\Controller;

use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\ComponentRepositoryInterface;
use Yireo\LokiComponents\Filter\Filter;
use Yireo\LokiComponents\Validator\Validator;

class RepositoryDispatcher
{
    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
        private readonly EventManager $eventManager,
        private readonly Filter $filter,
        private readonly Validator $validator,
    ) {
    }

    public function dispatch(AbstractBlock $block, mixed $componentData): void
    {
        $component = $this->componentRegistry->getComponentFromBlock($block);
        $this->eventManager->dispatch('loki_components_repository_dispatch', ['component' => $component]);

        $repository = $component->getRepository();
        if (false === $repository instanceof ComponentRepositoryInterface) {
            return;
        }

        $componentData = $this->filter->filter($component->getFilters(), $componentData);
        if (true === $this->validator->validate($component, $componentData)) {
            $repository->saveValue($componentData);
        }
    }
}
