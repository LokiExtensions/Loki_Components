<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Mock;

use BadMethodCallException;
use Loki\Components\Component\AbstractComponentContext;

trait ComponentContextMock
{
    protected function getComponentContextMock(array $contextDependencies = []): AbstractComponentContext
    {
        $context = $this->getMockBuilder(AbstractComponentContext::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__call'])
            ->getMock();

        $context->method('__call')
            ->willReturnCallback(function (string $name, array $args) use ($contextDependencies) {
                if ($args !== []) {
                    throw new BadMethodCallException("Unexpected args for $name");
                }

                if (array_key_exists($name, $contextDependencies)) {
                    return $contextDependencies[$name];
                }

                throw new BadMethodCallException("Unexpected method: $name");
            });

        return $context;
    }
}
