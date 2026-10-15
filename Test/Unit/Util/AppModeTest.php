<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use Loki\Components\Util\AppMode;
use Magento\Framework\App\State as AppState;
use PHPUnit\Framework\TestCase;

class AppModeTest extends TestCase
{
    public function testIsDeveloperMode(): void
    {
        $this->assertTrue($this->getAppMode(AppState::MODE_DEVELOPER)->isDeveloperMode());
        $this->assertFalse($this->getAppMode(AppState::MODE_PRODUCTION)->isDeveloperMode());
        $this->assertFalse($this->getAppMode(AppState::MODE_DEFAULT)->isDeveloperMode());
    }

    private function getAppMode(string $mode): AppMode
    {
        $appState = $this->createMock(AppState::class);
        $appState->method('getMode')->willReturn($mode);

        return new AppMode($appState);
    }
}
