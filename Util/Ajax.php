<?php
declare(strict_types=1);

namespace Loki\Components\Util;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Ajax implements ArgumentInterface
{
    public function __construct(
        private readonly HttpRequest $httpRequest
    ) {
    }

    public function isAjax(): bool
    {
        $value = $this->httpRequest->getHeader('X-Alpine-Request');
        return in_array($value, [1, 'true', true]);
    }
}
