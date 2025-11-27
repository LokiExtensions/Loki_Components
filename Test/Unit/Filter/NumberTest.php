<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Number;

class NumberTest extends TestCase
{
    public function testFilter()
    {
        $filterScope = new FilterScope();
        $filter = new Number();
        $this->assertEquals(42, $filter->filter('42', $filterScope));
        $this->assertEquals(0, $filter->filter([], $filterScope));
        $this->assertEquals(0, $filter->filter('Foobar', $filterScope));
    }
}
