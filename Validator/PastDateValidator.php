<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;

class PastDateValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        $date = trim((string)$value);
        if ($date === '' || $date === '0') {
            return true;
        }

        if (strtotime($date) > time()) {
            return ['Date needs to be in the past'];
        }

        return true;
    }
}
