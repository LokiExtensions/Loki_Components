<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Component;

use Loki\Components\Component\ComponentContext;
use PHPUnit\Framework\TestCase;
use stdClass;

class ComponentContextTest extends TestCase
{
    public function testReturnsInjectedDependencies(): void
    {
        $customerSession = new class {
            public function __call(string $method, array $arguments): mixed
            {
                return null;
            }
        };
        $urlFactory = new stdClass();

        $context = new ComponentContext([
            'customerSession' => $customerSession,
            'urlFactory' => $urlFactory,
        ]);

        $this->assertSame($customerSession, $context->getCustomerSession());
        $this->assertSame($urlFactory, $context->getUrlFactory());
    }
}
