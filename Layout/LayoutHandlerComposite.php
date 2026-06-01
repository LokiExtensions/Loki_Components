<?php declare(strict_types=1);

namespace Loki\Components\Layout;

use Magento\Framework\View\LayoutInterface;

class LayoutHandlerComposite
{
    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly array $layoutHandlers = []
    ) {
    }

    public function execute(array $handles): void
    {
        $originalHandles = $handles;
        $handles = $this->getHandles($handles);
        foreach ($handles as $handle) {
            $this->layout->getUpdate()->addHandle($handle);
        }

        foreach (array_diff($originalHandles, $handles) as $handle) {
            $this->layout->getUpdate()->removeHandle($handle);
        }
    }

    public function getHandles(array $handles): array
    {
        foreach ($this->layoutHandlers as $layoutHandler) {
            if (false === $layoutHandler instanceof LayoutHandlerInterface) {
                continue;
            }

            $handles = $layoutHandler->get($handles);
        }

        return array_unique($handles);
    }
}
