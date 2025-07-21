<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\PositiveNumber;

class PositiveNumberTest extends TestCase
{
    public function testFilter()
    {
        $filter = new PositiveNumber();
        $this->assertEquals(42, $filter->filter(-42));
        $this->assertEquals(42, $filter->filter('-42'));
        $this->assertEquals(42, $filter->filter('42'));
    }
}
