<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Messages;

use Yireo\LokiComponents\Component\ComponentViewModel;

class MessagesViewModel extends ComponentViewModel
{
    public function getJsComponentName(): ?string
    {
        return 'LokiComponentMessages';
    }

    public function getJsData(): ?array
    {
        return [
            'reload' => $this->getMessageManager()->hasGlobalMessages() // @todo: This is always true?
        ];
    }
}
