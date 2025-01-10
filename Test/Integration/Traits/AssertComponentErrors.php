<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Yireo\LokiComponents\Component\ComponentInterface;

trait AssertComponentErrors
{
    protected function assertComponentHasError(ComponentInterface $component, $expectedError)
    {
        $messages = $component->getLocalMessageRegistry()->getMessages();
        $found = false;
        $foundMessages = [];
        foreach ($messages as $message) {
            if ($message->getComponentName() !== $component->getName()) {
                continue;
            }

            $foundMessages[] = $message->getText();
            if ($message->getText() === $expectedError) {
                $found = true;
            }
        }

        $this->assertTrue(
            $found,
            'Component "'.$component->getName().'" does not have error "'.$expectedError.'":'."\n"
            .implode("\n", $foundMessages)
        );
    }

    protected function assertComponentHasErrors(ComponentInterface $component)
    {
        $messages = $component->getLocalMessageRegistry()->getMessages();
        $found = false;
        foreach ($messages as $message) {
            if ($message->getComponentName() !== $component->getName()) {
                continue;
            }

            $found = true;
        }

        $this->assertTrue($found, 'Component "'.$component->getName().'" has no errors');
    }

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

        $this->assertFalse($found, 'Component "'.$component->getName().'" has errors: '.var_export($messages, true));
    }
}
