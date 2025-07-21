<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Lowercase;

class LowercaseTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Lowercase();
        $this->assertEquals('foobar', $filter->filter('Foobar'));
    }
}
