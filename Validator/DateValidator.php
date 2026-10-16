<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;

class DateValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        $date = trim((string)$value, " \f\n\r\t\v\x00");
        if ($date === '' || $date === '0') {
            return true;
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $match)
            && checkdate((int)$match[2], (int)$match[3], (int)$match[1])) {
            return true;
        }

        return ['Invalid date format'];
    }
}
