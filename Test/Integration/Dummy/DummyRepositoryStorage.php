<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Dummy;

use Throwable;

class DummyRepositoryStorage
{
    private array $values = [];
    private array $saveOrder = [];
    private array $priorities = [];
    private array $exceptions = [];

    public function getValue(string $componentName): mixed
    {
        return $this->values[$componentName] ?? null;
    }

    public function hasValue(string $componentName): bool
    {
        return array_key_exists($componentName, $this->values);
    }

    public function saveValue(string $componentName, mixed $value): void
    {
        if (isset($this->exceptions[$componentName])) {
            throw $this->exceptions[$componentName];
        }

        $this->values[$componentName] = $value;
        $this->saveOrder[] = $componentName;
    }

    public function getSaveOrder(): array
    {
        return $this->saveOrder;
    }

    public function getPriority(string $componentName): int
    {
        return $this->priorities[$componentName] ?? 0;
    }

    public function setPriority(string $componentName, int $priority): void
    {
        $this->priorities[$componentName] = $priority;
    }

    public function throwOnSave(string $componentName, Throwable $exception): void
    {
        $this->exceptions[$componentName] = $exception;
    }

    public function reset(): void
    {
        $this->values = [];
        $this->saveOrder = [];
        $this->priorities = [];
        $this->exceptions = [];
    }
}
