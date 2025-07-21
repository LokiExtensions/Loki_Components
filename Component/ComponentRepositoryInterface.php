<?php
declare(strict_types=1);

namespace Loki\Components\Component;

interface ComponentRepositoryInterface
{
    public function setComponent(ComponentInterface $component): void;

    public function saveValue(mixed $value): void;

    public function getValue(): mixed;
    public function getDefaultValue(): mixed;

}
