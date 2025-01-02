<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Messages;

use Magento\Framework\App\ObjectManager;
use Yireo\LokiComponents\Component\ComponentContext;
use Yireo\LokiComponents\Messages\GlobalMessageManager;

class MessagesContext extends ComponentContext
{
    public function getGlobalMessageManager(): GlobalMessageManager
    {
        return ObjectManager::getInstance()->get(GlobalMessageManager::class);
    }
}
