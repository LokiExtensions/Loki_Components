<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Config\Config;

class EmailValidator implements ValidatorInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        $email = trim((string)$value);

        if ($email === '' || $email === '0') {
            return true;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['Invalid email address'];
        }

        if ($this->config->enableMxValidationForEmail()) {
            $result = $this->checkMxRecord($email);
            if (is_array($result)) {
                return $result;
            }
        }

        return true;
    }

    private function checkMxRecord(string $email): bool|array
    {
        $parts = explode('@', $email);
        $domain = $parts[1];
        if (false === checkdnsrr($domain)) {
            $message = (string)__('Domain "%s" is not reachable for mail');

            return [sprintf($message, $domain)];
        }

        return true;
    }
}
