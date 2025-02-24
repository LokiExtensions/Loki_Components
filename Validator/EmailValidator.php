<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Magento\Customer\Api\AccountManagementInterface;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Config\Config;

class EmailValidator implements ValidatorInterface
{
    public function __construct(
        private AccountManagementInterface $accountManagement,
        private Config $config
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): true|array
    {
        $email = trim((string)$value);

        if (empty($email)) {
            return true;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['Invalid email'];
        }

        if ($this->config->enableMxValidationForEmail()) {
            $result = $this->checkMxRecord($email);
            if (is_array($result)) {
                return $result;
            }
        }

        // Mental note, this only triggers if `checkout/options/enable_guest_checkout_login` is enabled
        if (false === $this->accountManagement->isEmailAvailable($email)) {
            return ['This email address is not available'];
        }

        return true;
    }

    private function checkMxRecord(string $email): true|array
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
