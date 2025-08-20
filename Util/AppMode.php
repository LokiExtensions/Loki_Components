<?php
declare(strict_types=1);

namespace Loki\Components\Util;

use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AppMode implements ArgumentInterface
{
    public function __construct(
        private readonly AppState $appState
    ) {
    }

    public function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === AppState::MODE_DEVELOPER;
    }
}
