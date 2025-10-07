<?php declare(strict_types=1);

namespace Loki\Components\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Config implements ArgumentInterface
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function isDebug(): bool
    {
        return (bool)$this->scopeConfig->getValue('loki_components/general/debug');
    }

    public function enableMxValidationForEmail(): bool
    {
        return (bool)$this->scopeConfig->getValue('loki_components/validators/enable_mx_validation_for_email');
    }
}
