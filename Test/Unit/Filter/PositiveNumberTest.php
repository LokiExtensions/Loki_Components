<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Filter\Number;
use Yireo\LokiComponents\Filter\PositiveNumber;

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
