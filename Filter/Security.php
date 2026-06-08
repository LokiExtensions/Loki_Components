<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class Security implements FilterInterface
{
    public function filter(mixed $value, FilterScope $scope): mixed
    {
       if (is_object($value)) {
            return $value;
        }

        $value = (string)$value;

        do {
            $previous = $value;
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
            $value = strip_tags($value);
        } while ($value !== $previous);

        $value = preg_replace('/(?:javascript|vbscript|data)\s*:/i', '', $value);

        return trim($value);
    }
}
