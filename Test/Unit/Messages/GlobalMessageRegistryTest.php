<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Messages;

use Loki\Components\Messages\GlobalMessageRegistry;
use Magento\Framework\Message\Manager as MessageManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GlobalMessageRegistryTest extends TestCase
{
    #[DataProvider('getMethods')]
    public function testDelegatesToMessageManager(string $registryMethod, string $messageManagerMethod): void
    {
        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->once())->method($messageManagerMethod)->with('Hello');

        (new GlobalMessageRegistry($messageManager))->$registryMethod('Hello');
    }

    public static function getMethods(): array
    {
        return [
            ['addSuccess', 'addSuccessMessage'],
            ['addNotice', 'addNoticeMessage'],
            ['addWarning', 'addWarningMessage'],
            ['addError', 'addErrorMessage'],
        ];
    }
}
