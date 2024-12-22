<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Mutator;

use Yireo\LokiComponents\Component\MutableComponentInterface;

interface MutatorInterface
{
    public function getComponent(): ?MutableComponentInterface;

    public function setComponent(MutableComponentInterface $component): void;

    public function hasComponent(): bool;

    public function mutate(mixed $data): void;
}
