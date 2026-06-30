<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Loki\Components\Attribute\JsProperty;
use Loki\Components\Component\ComponentViewModelInterface;
use ReflectionClass;

class JsPropertyResolver
{
    public function getJsProperties(ComponentViewModelInterface $componentViewModel): array
    {
        $reflection = new ReflectionClass($componentViewModel::class);
        $jsProperties = [];

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(JsProperty::class);
            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();
                $jsProperties[$instance->name] = $method->invoke($componentViewModel);
            }
        }

        return $jsProperties;
    }
}
