<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

interface ComponentRepositoryInterface
{
    public function save(mixed $data): void;

    public function get(): mixed;
}
