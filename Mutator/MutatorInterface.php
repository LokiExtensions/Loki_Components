<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Mutator;

interface MutatorInterface
{
    public function mutate(array $data = []): void;
}
