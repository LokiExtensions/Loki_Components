<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Messages;

use Yireo\LokiComponents\Component\ComponentViewModel;

/**
 * @method MessagesContext getContext()
 */
class MessagesViewModel extends ComponentViewModel
{
    public function getJsComponentName(): ?string
    {
        return 'LokiComponentMessages';
    }

    /**
     * @return array|null
     */
    public function getJsData(): ?array
    {
        return [
            'messages' => $this->getContext()->getGlobalMessageManager()->toArray(),
        ];
    }
}
