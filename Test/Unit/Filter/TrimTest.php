<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Trim;

class TrimTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Trim();
        $this->assertEquals('foobar', $filter->filter(' foobar '));
        $this->assertEquals('42', $filter->filter(42));
    }
}
