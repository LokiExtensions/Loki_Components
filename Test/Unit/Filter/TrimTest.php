<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Filter\Number;
use Yireo\LokiComponents\Filter\Trim;

class TrimTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Trim();
        $this->assertEquals('foobar', $filter->filter(' foobar '));
        $this->assertEquals('42', $filter->filter(42));
    }
}
