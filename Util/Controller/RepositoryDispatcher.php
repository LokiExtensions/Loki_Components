<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Component\ComponentInterface;
use Magento\Framework\Event\Manager as EventManager;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Validator\Validator;
use InvalidArgumentException;
use Magento\Framework\View\LayoutInterface;

class RepositoryDispatcher
{
    public function __construct(
        private readonly EventManager $eventManager,
        private readonly Filter $filter,
        private readonly Validator $validator,
        private readonly int $maxValueLength = 65536,
        private readonly int $maxElements = 1000,
    ) {
    }

    public function dispatch(ComponentInterface $component, mixed $componentData): void
    {
        $this->eventManager->dispatch('loki_components_repository_dispatch', ['component' => $component]);

        $repository = $component->getRepository();
        if (false === $repository instanceof ComponentRepositoryInterface) {
            return;
        }

        if ($this->getValueLength($componentData) > $this->maxValueLength) {
            throw new InvalidArgumentException((string)__('The submitted value is too long'));
        }

        if (is_array($componentData) && count($componentData, COUNT_RECURSIVE) > $this->maxElements) {
            throw new InvalidArgumentException((string)__('The submitted value is too long'));
        }

        $componentData = $this->filter->filter($component, $componentData);
        if (false === $this->validator->validate($component, $componentData)) {
            return;
        }

        $repository->saveValue($componentData);
    }

    private function getValueLength(mixed $value): int
    {
        if (is_string($value)) {
            return strlen($value);
        }

        if (false === is_array($value)) {
            return is_scalar($value) ? strlen((string)$value) : 1;
        }

        $length = 1;
        foreach ($value as $key => $subValue) {
            $length += 1 + strlen((string)$key) + $this->getValueLength($subValue);
            if ($length > $this->maxValueLength) {
                break;
            }
        }

        return $length;
    }

    public function postDispatch(LayoutInterface $layout, array $updates): void
    {
        $this->eventManager->dispatch(
            'loki_components_repository_post_dispatch',
            [
                'layout' => $layout,
                'updates' => $updates
            ]
        );
    }
}
