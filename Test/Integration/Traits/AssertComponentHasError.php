<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Yireo\LokiComponents\Component\ComponentInterface;

trait AssertComponentHasError
{
    public function assertComponentHasError(ComponentInterface $component, $error)
    {
        $messages = $component->getLocalMessageRegistry()->getMessages();
        $found = false;
        foreach ($messages as $message) {
            if ($message->getComponentName() !== $component->getName()) {
                continue;
            }

            if ($message === $error) {
                $found = true;
            }
        }

        $this->assertTrue($found);
    }
}
