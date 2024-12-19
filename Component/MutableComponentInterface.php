<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

interface MutableComponentInterface extends ComponentInterface
{
    public function mutate(array $data = []): void;
}
