<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\GlobalMessages;

use Yireo\LokiComponents\Component\ComponentViewModel;

/**
 * @method GlobalMessagesContext getContext()
 */
class GlobalMessagesViewModel extends ComponentViewModel
{
    public function getJsComponentName(): ?string
    {
        return 'LokiComponentsGlobalMessages';
    }

    /**
     * @return array|null
     */
    public function getJsData(): array
    {
        return [
            'timeout' => $this->getContext()->getConfig()->getGlobalMessagesTimeout(),
        ];
    }
}
