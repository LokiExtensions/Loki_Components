<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Loki\Components\Attribute\JsProperty;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Exception\InvalidJsPropertyException;
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

        $class = new ReflectionClass($className);
        $this->assertNoNonPublicJsProperties($class);

        $jsPropertyMethods = [];
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($this->getJsPropertyNames($method) as $jsPropertyName) {
                $jsPropertyMethods[$jsPropertyName] = $method;
            }
        }

        $this->jsPropertyMethods[$className] = $jsPropertyMethods;

        return $jsPropertyMethods;
    }

    private function assertNoNonPublicJsProperties(ReflectionClass $class): void
    {
        while (false !== $class) {
            $methods = $class->getMethods(ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE);
            foreach ($methods as $method) {
                if ($method->getDeclaringClass()->getName() !== $class->getName()) {
                    continue;
                }

                foreach ($method->getAttributes(JsProperty::class) as $attribute) {
                    throw new InvalidJsPropertyException(sprintf(
                        '#[JsProperty(name: "%s")] is only allowed on public methods: %s::%s()',
                        $attribute->newInstance()->name,
                        $class->getName(),
                        $method->getName()
                    ));
                }
            }

            $class = $class->getParentClass();
        }
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
            if (false === $classMethod->isPublic()) {
                break;
            }
        }

        return array_values(array_unique($jsPropertyNames));
    }
}
