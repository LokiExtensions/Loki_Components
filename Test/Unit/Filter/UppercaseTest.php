<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Uppercase;

class UppercaseTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Uppercase();
        $this->assertEquals('FOOBAR', $filter->filter('Foobar'));
    }
}
