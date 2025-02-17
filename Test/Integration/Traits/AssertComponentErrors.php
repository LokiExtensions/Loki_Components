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
        return $this->assertNotEmpty($component->getLocalMessageRegistry()->getMessages());
    }

    protected function assertComponentHasNoErrors(ComponentInterface $component)
    {
        return $this->assertEmpty($component->getLocalMessageRegistry()->getMessages());
    }
}
