<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Filter\FilterScope;
use Loki\Components\Filter\RemoveSpaces;
use PHPUnit\Framework\TestCase;

class RemoveSpacesTest extends TestCase
{
    public function testFilter()
    {
        $filterScope = new FilterScope();
        $filter = new RemoveSpaces();
        $this->assertEquals('foobar', $filter->filter(' foo bar ', $filterScope));
        $this->assertEquals('1234AB', $filter->filter("1234\t AB\n", $filterScope));
        $this->assertSame('42', $filter->filter(42, $filterScope));
    }
}
