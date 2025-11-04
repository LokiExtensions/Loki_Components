<?php
declare(strict_types=1);

namespace Loki\Components\Util;

use Loki\Components\Component\ComponentInterface;

class IsEmpty
{
    public function execute(ComponentInterface $component, mixed $value): bool
    {
        if ($this->shouldBeNumber($component) && is_numeric($value)) {
            return false;
        }

        if ($this->shouldBePositiveNumber($component) && is_numeric($value) && $value >= 0) {
            return false;
        }

        if (is_string($value) && strlen($value) > 0) {
            return false;
        }

        return empty($value);
    }

    private function shouldBePositiveNumber(ComponentInterface $component): bool
    {
        $filters = $component->getFilters();

        return in_array('positive_number', $filters, true);
    }

    private function shouldBeNumber(ComponentInterface $component): bool
    {
        $filters = $component->getFilters();

        return in_array('number', $filters, true);
    }
}
