<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Trim;

class TrimTest extends TestCase
{
    public function testFilter()
    {
        $filterScope = new FilterScope();
        $filter = new Trim();
        $this->assertEquals('foobar', $filter->filter(' foobar ', $filterScope));
        $this->assertEquals('42', $filter->filter(42, $filterScope));
    }
}
