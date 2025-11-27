<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Lowercase;

class LowercaseTest extends TestCase
{
    public function testFilter()
    {
        $filterScope = new FilterScope();
        $filter = new Lowercase();
        $this->assertEquals('foobar', $filter->filter('Foobar', $filterScope));
    }
}
