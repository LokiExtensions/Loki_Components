<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Component;

use Loki\Components\Component\AbstractComponentContext;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use stdClass;

class AbstractComponentContextTest extends TestCase
{
    public function testReturnsDependencyByGetter(): void
    {
        $dependency = new stdClass();
        $context = new AbstractComponentContext([], ['customerSession' => $dependency]);

        $this->assertSame($dependency, $context->getCustomerSession());
    }

    public function testFallsBackToParentContexts(): void
    {
        $dependency = new stdClass();
        $context = new AbstractComponentContext([
            new AbstractComponentContext([], ['foo' => new stdClass()]),
            new AbstractComponentContext([], ['bar' => $dependency]),
        ]);

        $this->assertSame($dependency, $context->getBar());
    }

    public function testOwnDependencyOverrulesParentContext(): void
    {
        $dependency = new stdClass();
        $context = new AbstractComponentContext(
            [new AbstractComponentContext([], ['foo' => new stdClass()])],
            ['foo' => $dependency]
        );

        $this->assertSame($dependency, $context->getFoo());
    }

    public function testThrowsExceptionForUnknownDependency(): void
    {
        $context = new AbstractComponentContext([], ['foo' => new stdClass()]);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessageMatches('/getUnknown. No object "unknown": foo/');
        $context->getUnknown();
    }

    public function testExceptionContainsErrorsOfParentContexts(): void
    {
        $context = new AbstractComponentContext([new AbstractComponentContext([], ['bar' => new stdClass()])]);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessageMatches('/Exceptions: Invalid method .*getUnknown/');
        $context->getUnknown();
    }
}
