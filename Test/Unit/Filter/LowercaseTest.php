<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Filter\Lowercase;

class LowercaseTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Lowercase();
        $this->assertEquals('foobar', $filter->filter('Foobar'));
    }
}
