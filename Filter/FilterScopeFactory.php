<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Magento\Framework\ObjectManagerInterface;

class FilterScopeFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ){
    }

    /**
     * @phpstan-return FilterScope
     */
    public function create(): FilterScope
    {
        /** @var FilterScope $filterScope */
        $filterScope = $this->objectManager->create(FilterScope::class);
        return $filterScope;
    }
}
