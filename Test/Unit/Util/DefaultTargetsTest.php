<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use Loki\Components\Util\DefaultTargets;
use PHPUnit\Framework\TestCase;

class DefaultTargetsTest extends TestCase
{
    public function testGetTargets(): void
    {
        $this->assertSame([], (new DefaultTargets())->getTargets());
        $this->assertSame(['foo', 'bar'], (new DefaultTargets(['foo', 'bar']))->getTargets());
    }
}
