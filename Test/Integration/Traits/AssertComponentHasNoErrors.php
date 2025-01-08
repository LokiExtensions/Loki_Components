<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Yireo\LokiComponents\Component\ComponentInterface;

trait AssertComponentHasNoErrors
{
    protected function assertComponentHasNoErrors(ComponentInterface $component)
    {
        $messages = $component->getLocalMessageRegistry()->getMessages();
        $found = false;
        foreach ($messages as $message) {
            if ($message->getComponentName() !== $component->getName()) {
                continue;
            }

            $found = true;
        }

        $this->assertFalse($found);
    }
}
