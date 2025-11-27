<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Uppercase;

class UppercaseTest extends TestCase
{
    public function testFilter()
    {
        $filterScope = new FilterScope();
        $filter = new Uppercase();
        $this->assertEquals('FOOBAR', $filter->filter('Foobar', $filterScope));
    }
}
