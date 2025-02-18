<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Config implements ArgumentInterface
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function onlyValidateAjax(): bool
    {
        return (bool)$this->scopeConfig->getValue('yireo_loki_components/general/only_validate_ajax');
    }
}
