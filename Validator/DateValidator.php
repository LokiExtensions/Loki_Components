<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;

class DateValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): true|array
    {
        $date = trim((string)$value);
        if (empty($date)) {
            return true;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return true;
        }

        return ['Invalid date format'];
    }
}
