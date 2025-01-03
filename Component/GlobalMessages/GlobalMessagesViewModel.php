<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\GlobalMessages;

use Yireo\LokiComponents\Component\ComponentViewModel;

class GlobalMessagesViewModel extends ComponentViewModel
{
    public function getJsComponentName(): ?string
    {
        return 'LokiComponentGlobalMessages';
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
        ];
    }
}
