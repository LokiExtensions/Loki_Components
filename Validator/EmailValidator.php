<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;

class EmailValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        $email = trim((string)$value);

        if ($email === '' || $email === '0') {
            return true;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['Invalid email address'];
        }

        return true;
    }
}
