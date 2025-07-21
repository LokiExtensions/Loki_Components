<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Traits;

use Loki\Components\Component\ComponentInterface;

trait AssertComponentErrors
{
    protected function assertComponentHasError(ComponentInterface $component, $expectedError)
    {
        $messages = $component->getLocalMessageRegistry()->getMessages();
        $found = false;
        $foundMessages = [];
        foreach ($messages as $message) {
            $foundMessages[] = $message->getText();
            if (str_contains($message->getText(), $expectedError)) {
                $found = true;
            }
        }

        $this->assertTrue(
            $found,
            'Component "' . $component->getName() . '" does not have error "' . $expectedError . '"' . "\n"
            . 'Actual messages: ' . implode("\n", $foundMessages)
        );
    }

    protected function assertComponentHasErrors(ComponentInterface $component)
    {
        $this->assertNotEmpty($component->getLocalMessageRegistry()->getMessages());
    }

    protected function assertComponentHasNoErrors(ComponentInterface $component)
    {
        $this->assertEmpty($component->getLocalMessageRegistry()->getMessages());
    }
}
