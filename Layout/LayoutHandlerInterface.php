<?php
declare(strict_types=1);

namespace Loki\Components\Layout;

interface LayoutHandlerInterface
{
    /**
     * @param $handles string[]
     * @return string[]
     */
    public function get(array $handles): array;
}
