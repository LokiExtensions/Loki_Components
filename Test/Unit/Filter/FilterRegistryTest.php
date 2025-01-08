<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Filter\FilterInterface;
use Yireo\LokiComponents\Filter\FilterRegistry;

class FilterRegistryTest extends TestCase
{
    public function testGetSelectedFilters()
    {
        $filter1 = $this->createMock(FilterInterface::class);
        $filter2 = $this->createMock(FilterInterface::class);

        $filterRegistry = new FilterRegistry(['item1' => $filter1, 'item2' => $filter2]);
        $this->assertCount(2, $filterRegistry->getApplicableFilters(['item1', 'item2', 'item3']));
    }
}
