<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Index;

use Loki\Components\Controller\Index\Test;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\Page;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class TestTest extends TestCase
{
    public function testReturnsPageInDeveloperMode(): void
    {
        $this->assertInstanceOf(Page::class, $this->getController(AppState::MODE_DEVELOPER)->execute());
    }

    #[DataProvider('getNonDeveloperModes')]
    public function testIsNotFoundOutsideDeveloperMode(string $mode): void
    {
        $this->expectException(NotFoundException::class);
        $this->getController($mode)->execute();
    }

    public static function getNonDeveloperModes(): array
    {
        return [
            [AppState::MODE_PRODUCTION],
            [AppState::MODE_DEFAULT],
        ];
    }

    private function getController(string $mode): Test
    {
        $appState = $this->createMock(AppState::class);
        $appState->method('getMode')->willReturn($mode);

        return ObjectManager::getInstance()->create(Test::class, ['appState' => $appState]);
    }
}
