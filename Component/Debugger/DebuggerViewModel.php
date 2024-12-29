<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Debugger;

use Yireo\LokiComponents\Component\ComponentViewModel;

/**
 * @method DebuggerContext getContext()
 */
class DebuggerViewModel extends ComponentViewModel
{
    public function getJsComponentName(): ?string
    {
        return 'LokiComponentsDebugger';
    }

    public function getData(): array
    {
        return $this->getContext()->getDebugger()->getData();
    }
}
