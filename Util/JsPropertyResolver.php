<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Loki\Components\Attribute\JsProperty;
use Loki\Components\Component\ComponentViewModelInterface;
use ReflectionClass;
use ReflectionMethod;

class JsPropertyResolver
{
    private array $jsPropertyMethods = [];

    public function getJsProperties(ComponentViewModelInterface $componentViewModel): array
    {
        $jsProperties = [];
        foreach ($this->getJsPropertyMethods($componentViewModel::class) as $jsPropertyName => $method) {
            $jsProperties[$jsPropertyName] = $method->invoke($componentViewModel);
        }

        return $jsProperties;
    }

    private function getJsPropertyMethods(string $className): array
    {
        if (isset($this->jsPropertyMethods[$className])) {
            return $this->jsPropertyMethods[$className];
        }

        $jsPropertyMethods = [];
        foreach ((new ReflectionClass($className))->getMethods() as $method) {
            foreach ($this->getJsPropertyNames($method) as $jsPropertyName) {
                $jsPropertyMethods[$jsPropertyName] = $method;
            }
        }

        $this->jsPropertyMethods[$className] = $jsPropertyMethods;

        return $jsPropertyMethods;
    }

    private function getJsPropertyNames(ReflectionMethod $method): array
    {
        $jsPropertyNames = [];
        $methodName = $method->getName();
        $classMethod = $method;

        while (true) {
            foreach ($classMethod->getAttributes(JsProperty::class) as $attribute) {
                $jsPropertyNames[] = $attribute->newInstance()->name;
            }

            $parentClass = $classMethod->getDeclaringClass()->getParentClass();
            if (false === $parentClass || false === $parentClass->hasMethod($methodName)) {
                break;
            }

            $classMethod = $parentClass->getMethod($methodName);
            if ($classMethod->isPrivate()) {
                break;
            }
        }

        return array_values(array_unique($jsPropertyNames));
    }
}
