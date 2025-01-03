<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Messages;

use Magento\Framework\App\ObjectManager;
use Yireo\LokiComponents\Component\ComponentContext;
use Yireo\LokiComponents\Messages\GlobalMessageRegistry;

// @todo: Rename to GlobalMessagesContext
class MessagesContext extends ComponentContext
{
    public function getGlobalMessageRegistry(): GlobalMessageRegistry
    {
        return ObjectManager::getInstance()->get(GlobalMessageRegistry::class);
    }
}
