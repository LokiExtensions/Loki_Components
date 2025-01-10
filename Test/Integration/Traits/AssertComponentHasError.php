<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Yireo\LokiComponents\Component\ComponentInterface;

trait AssertComponentHasError
{
    protected function assertComponentHasError(ComponentInterface $component, $expectedError)
    {
        $messages = $component->getLocalMessageRegistry()->getMessages();
        $found = false;
        foreach ($messages as $message) {
            if ($message->getComponentName() !== $component->getName()) {
                continue;
            }

            if ($message === $expectedError) {
                $found = true;
            }
        }

        $this->assertTrue($found, 'Component "'.$component->getName().'" does not have error "'.$expectedError.'": '.var_export($messages, true));
    }
}
