<?php
declare(strict_types=1);

namespace Loki\Components\Component;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Throwable;

class AbstractComponentContext implements ComponentContextInterface
{
    public function __construct(
        protected array $parentContexts = [],
        protected array $dependencies = []
    ) {
    }

    public function __call(string $methodName, array $arguments)
    {
        $objectName = lcfirst(substr($methodName, 3));
        if (str_starts_with($methodName, 'get')) {
            if (array_key_exists($objectName, $this->dependencies)) {
                return $this->dependencies[$objectName];
            }
        }

        foreach ($this->parentContexts as $parentContext) {
            try {
                return $parentContext->{$methodName}(...$arguments);
            } catch (Throwable $e) {
            }
        }

        throw new LocalizedException(
            new Phrase(
                'Invalid method %1::%2. No object "%3": %4',
                [
                    get_class($this),
                    $methodName,
                    $objectName,
                    implode(', ', array_keys($this->dependencies)),
                ]
            )
        );
    }
}
