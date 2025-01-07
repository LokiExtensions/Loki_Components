<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

interface ComponentRepositoryInterface
{
    public function setComponent(ComponentInterface $component): void;

    public function saveValue(mixed $data): void;

    public function getValue(): mixed;

}
