<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Messages\LocalMessageRegistry;

interface ComponentRepositoryInterface
{
    public function save(mixed $data): void;

    public function get(): mixed;

    public function getLocalMessageRegistry(): LocalMessageRegistry;
}
