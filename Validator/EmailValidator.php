<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Magento\Customer\Api\AccountManagementInterface;
use Yireo\LokiComponents\Component\ComponentInterface;

class EmailValidator implements ValidatorInterface
{
    public function __construct(
        private AccountManagementInterface $accountManagement,
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

        $parts = explode('@', $email);
        $domain = $parts[1];
        if (false === checkdnsrr($domain)) {
            $message = (string)__('Domain "%s" does not seem to be valid');
            return [sprintf($message, $domain)];
        }

        // Mental note, this only triggers if `checkout/options/enable_guest_checkout_login` is enabled
        if (false === $this->accountManagement->isEmailAvailable($email)) {
            return ['This email address is not available'];
        }

        return true;
    }
}
