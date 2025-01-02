<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Messages\MessageManager;

interface ComponentRepositoryInterface
{
    public function save(mixed $data): void;

    public function get(): mixed;

    public function getLocalMessageManager(): MessageManager;
}
