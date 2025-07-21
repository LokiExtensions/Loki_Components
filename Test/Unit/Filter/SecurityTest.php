<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Security;

class SecurityTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Security();
        $this->assertEquals('foobar', $filter->filter('<script>foobar</script>'));
    }
}
