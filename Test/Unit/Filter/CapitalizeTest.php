<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Filter\Capitalize;

class CapitalizeTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Capitalize();
        $this->assertEquals('Foobar', $filter->filter('foobar'));
        $this->assertEquals('1foobar', $filter->filter('1foobar'));
    }
}
