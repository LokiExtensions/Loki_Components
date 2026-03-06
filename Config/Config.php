<?php declare(strict_types=1);

namespace Loki\Components\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ArgumentInterface
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function getPollingInterval(): int
    {
        return (int)$this->scopeConfig->getValue(
            'loki_components/general/polling_interval',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isDebug(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'loki_components/general/debug',
            ScopeInterface::SCOPE_STORE
        );
    }
}
