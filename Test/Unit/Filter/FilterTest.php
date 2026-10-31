<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Exception\RecursionException;
use Loki\Components\Filter\Capitalize;
use Loki\Components\Filter\Filter;
use Loki\Components\Filter\FilterInterface;
use Loki\Components\Filter\FilterRegistry;
use Loki\Components\Filter\FilterScope;
use Loki\Components\Filter\FilterScopeFactory;
use Loki\Components\Filter\Lowercase;
use Loki\Components\Filter\Trim;
use Loki\Components\Filter\Uppercase;
use Magento\Framework\View\Element\AbstractBlock;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testAppliesOnlyFiltersOfComponent(): void
    {
        $filter = $this->getFilter();
        $this->assertSame('FOO', $filter->filter($this->getComponent(['trim', 'uppercase']), ' foo '));
        $this->assertSame(' foo ', $filter->filter($this->getComponent(['unknown']), ' foo '));
    }

    public function testAppliesFiltersInOrderOfComponent(): void
    {
        $filter = $this->getFilter();
        $this->assertSame('foo', $filter->filter($this->getComponent(['capitalize', 'lowercase']), 'fOO'));
        $this->assertSame('Foo', $filter->filter($this->getComponent(['lowercase', 'capitalize']), 'fOO'));
    }

    public function testSkipsEmptyBooleanAndIntegerValues(): void
    {
        $filter = $this->getFilter();
        $component = $this->getComponent(['uppercase']);

        $this->assertNull($filter->filter($component, null));
        $this->assertSame('', $filter->filter($component, ''));
        $this->assertSame(0, $filter->filter($component, 0));
        $this->assertSame(42, $filter->filter($component, 42));
        $this->assertTrue($filter->filter($component, true));
        $this->assertFalse($filter->filter($component, false));
        $this->assertSame([], $filter->filter($component, []));
    }

    public function testFiltersArraysRecursivelyWithPropertyNames(): void
    {
        $spy = new class implements FilterInterface {
            public array $properties = [];

            public function filter(mixed $value, FilterScope $scope): mixed
            {
                $this->properties[] = $scope->getProperty();
                return strtoupper((string)$value);
            }
        };

        $filter = $this->getFilter(['spy' => $spy]);
        $result = $filter->filter($this->getComponent(['spy']), [
            'street' => 'main',
            'nested' => ['city' => 'amsterdam'],
        ]);

        $this->assertSame(['street' => 'MAIN', 'nested' => ['city' => 'AMSTERDAM']], $result);
        $this->assertSame(['street', 'city'], $spy->properties);
    }

    public function testSkipsUnfilteredPropertiesOfBlock(): void
    {
        $block = $this->createMock(AbstractBlock::class);
        $block->method('getData')->with('unfiltered_properties')->willReturn(['password']);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getFilters')->willReturn(['trim', 'uppercase']);
        $component->method('getBlock')->willReturn($block);

        $result = $this->getFilter()->filter($component, ['email' => ' foo ', 'password' => ' p<b>ss ']);
        $this->assertSame(['email' => 'FOO', 'password' => ' p<b>ss '], $result);
    }

    public function testPassesComponentToFilterScope(): void
    {
        $spy = new class implements FilterInterface {
            public ?ComponentInterface $component = null;

            public function filter(mixed $value, FilterScope $scope): mixed
            {
                $this->component = $scope->getComponent();
                return $value;
            }
        };

        $component = $this->getComponent(['spy']);
        $this->getFilter(['spy' => $spy])->filter($component, 'foo');
        $this->assertSame($component, $spy->component);
    }

    public function testThrowsRecursionExceptionWhenTooDeep(): void
    {
        $filter = $this->getFilter([], 2);
        $this->expectException(RecursionException::class);
        $filter->filter($this->getComponent(['trim']), ['a' => ['b' => ['c' => 'foo']]]);
    }

    private function getFilter(array $filters = [], int $recursionDepth = 10): Filter
    {
        if (empty($filters)) {
            $filters = [
                'trim' => new Trim(),
                'uppercase' => new Uppercase(),
                'lowercase' => new Lowercase(),
                'capitalize' => new Capitalize(),
            ];
        }

        $filterScopeFactory = $this->createMock(FilterScopeFactory::class);
        $filterScopeFactory->method('create')->willReturnCallback(fn () => new FilterScope());

        return new Filter(new FilterRegistry($filters), $filterScopeFactory, $recursionDepth);
    }

    private function getComponent(array $filters): ComponentInterface
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getFilters')->willReturn($filters);

        return $component;
    }
}
