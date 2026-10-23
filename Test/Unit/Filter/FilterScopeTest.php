<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\TestCase;

class FilterScopeTest extends TestCase
{
    public function testDefaults(): void
    {
        $filterScope = new FilterScope();

        $this->assertNull($filterScope->getComponent());
        $this->assertNull($filterScope->getProperty());
    }

    public function testSettersAndGetters(): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $filterScope = new FilterScope();
        $filterScope->setComponent($component);
        $filterScope->setProperty('email');

        $this->assertSame($component, $filterScope->getComponent());
        $this->assertSame('email', $filterScope->getProperty());

        $filterScope->setComponent(null);
        $this->assertNull($filterScope->getComponent());
    }
}
