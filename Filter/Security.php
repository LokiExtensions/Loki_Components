<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class Security implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
       if (is_object($value)) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->filter($val);
            }
        }

        $value = (string)$value;
        $value = strip_tags($value);
        $value = htmlentities($value);
        $value = htmlspecialchars_decode($value);
        return $value;
    }
}
