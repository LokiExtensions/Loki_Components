<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\GlobalMessages;

use Magento\Framework\App\ObjectManager;
use Yireo\LokiComponents\Component\ComponentContext;
use Yireo\LokiComponents\Messages\GlobalMessageRegistry;

class GlobalMessagesContext extends ComponentContext
{
    public function getGlobalMessageRegistry(): GlobalMessageRegistry
    {
        //return ObjectManager::getInstance()->get(GlobalMessageRegistry::class);
    }
}
