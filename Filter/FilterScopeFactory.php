<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Magento\Framework\ObjectManagerInterface;

class FilterScopeFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ){
    }

    public function create(): FilterScope
    {
        return $this->objectManager->create(FilterScope::class);
    }
}
