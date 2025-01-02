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
        $timeout = $this->getBlock()->getTimeout(); // @doc
        if (false === is_numeric($timeout)) {
            $timeout = 5000;
        }

        return [
            'timeout' => $timeout,
            'messages' => $this->getContext()->getGlobalMessageRegistry()->toArray(),
        ];
    }
}
