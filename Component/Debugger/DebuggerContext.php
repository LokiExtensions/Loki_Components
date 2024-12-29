<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Debugger;

use Magento\Framework\App\ObjectManager;
use Yireo\LokiComponents\Component\ComponentContext;
use Yireo\LokiComponents\Util\Debugger;

class DebuggerContext extends ComponentContext
{
    public function getDebugger(): Debugger
    {
        return ObjectManager::getInstance()->get(Debugger::class);
    }

}
