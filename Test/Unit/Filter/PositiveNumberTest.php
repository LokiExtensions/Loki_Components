<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\PositiveNumber;

class PositiveNumberTest extends TestCase
{
    public function testFilter()
    {
        $filterScope = new FilterScope();
        $filter = new PositiveNumber();
        $this->assertEquals(0, $filter->filter(-42, $filterScope));
        $this->assertEquals(0, $filter->filter('-42', $filterScope));
        $this->assertEquals(42, $filter->filter('42', $filterScope));
    }
}
