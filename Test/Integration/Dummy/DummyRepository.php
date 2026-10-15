<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Dummy;

use Loki\Components\Component\ComponentRepository;

class DummyRepository extends ComponentRepository
{
    public function __construct(
        private readonly DummyRepositoryStorage $storage
    ) {
    }

    public function getValue(): mixed
    {
        return $this->storage->getValue($this->getComponentName());
    }

    public function saveValue(mixed $value): void
    {
        $this->storage->saveValue($this->getComponentName(), $value);
    }

    public function getPriority(): int
    {
        return $this->storage->getPriority($this->getComponentName());
    }
}
