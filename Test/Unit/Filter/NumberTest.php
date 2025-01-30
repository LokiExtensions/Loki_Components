<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Filter\Number;

class NumberTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Number();
        $this->assertEquals(42, $filter->filter('42'));
        $this->assertEquals(0, $filter->filter([]));
        $this->assertEquals(0, $filter->filter('Foobar'));
    }
}
