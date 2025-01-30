<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Filter\Uppercase;

class UppercaseTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Uppercase();
        $this->assertEquals('FOOBAR', $filter->filter('Foobar'));
    }
}
