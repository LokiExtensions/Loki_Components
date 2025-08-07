<?php declare(strict_types=1);

namespace Loki\Components\Component\GlobalMessages;

use Loki\Components\Component\ComponentViewModel;

/**
 * @method GlobalMessagesContext getContext()
 */
class GlobalMessagesViewModel extends ComponentViewModel
{
    public function getJsComponentName(): ?string
    {
        return 'LokiComponentsGlobalMessages';
    }

    public function getJsData(): array
    {
        return [
            'timeout' => $this->getContext()->getConfig()->getGlobalMessagesTimeout(),
        ];
    }
}
