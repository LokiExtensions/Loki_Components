<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Behaviour;

trait InheritFromParentContext
{
    public function __call(string $name, array $arguments)
    {
        if (false === property_exists($this, 'parentContext')) {
            throw new \RuntimeException('Method "'.$name.'" not found in '.__CLASS__);
        }

        return call_user_func([$this->parentContext, $name], $arguments);
    }

}
