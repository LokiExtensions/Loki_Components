<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Block;

use Loki\Components\Util\Block\ImageRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[AppArea('frontend')]
class ImageRendererTest extends TestCase
{
    public function testIconReturnsInlineSvgWithDefaultSize(): void
    {
        $svg = $this->getImageRenderer()->icon($this->getBlock(), 'xmark');

        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringContainsString('width="20"', $svg);
        $this->assertStringContainsString('height="20"', $svg);
        $this->assertStringContainsString('x-ignore=""', $svg);
        $this->assertStringNotContainsString('<?xml', $svg);
    }

    public function testIconSizeAndAttributesCanBeConfigured(): void
    {
        $block = $this->getBlock(['icons' => ['xmark' => ['size' => 32]]]);
        $svg = $this->getImageRenderer()->icon($block, 'xmark', ['class' => 'text-red']);

        $this->assertStringContainsString('width="32"', $svg);
        $this->assertStringContainsString('height="32"', $svg);
        $this->assertStringContainsString('class="text-red"', $svg);
    }

    public function testIconWithAssetId(): void
    {
        $svg = $this->getImageRenderer()->icon($this->getBlock(), 'Loki_Components::images/xmark');
        $this->assertStringStartsWith('<svg', $svg);
    }

    public function testGetByUrl(): void
    {
        $this->assertSame(
            '<img src="https://example.com/logo.png" alt="Logo" loading="lazy" />',
            $this->getImageRenderer()->getByUrl('https://example.com/logo.png', ['alt' => 'Logo'])
        );
    }

    public function testGetWithUnknownImageReturnsImageTag(): void
    {
        $this->assertStringStartsWith('<img src="/media/foo.png"', $this->getImageRenderer()->get('/media/foo.png'));
    }

    public function testGetByAssetIdOfNonSvgReturnsImageTag(): void
    {
        $html = $this->getImageRenderer()->getByAssetId('Magento_Theme::favicon.ico', ['alt' => 'Favicon']);

        $this->assertStringStartsWith('<img src="', $html);
        $this->assertStringContainsString('favicon.ico', $html);
        $this->assertStringContainsString('alt="Favicon"', $html);
    }

    public function testGetByAssetIdRejectsInvalidAssetId(): void
    {
        $this->expectException(RuntimeException::class);
        $this->getImageRenderer()->getByAssetId('not-an-asset-id');
    }

    public function testGetByFileRejectsNonSvgFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->getImageRenderer()->getByFile('/tmp/foo.png');
    }

    public function testMissingIconFileOnlyReturnsCommentInDeveloperMode(): void
    {
        $output = $this->getImageRenderer()->getIconOutput('/does/not/exist.svg');

        $appState = ObjectManager::getInstance()->get(AppState::class);
        if ($appState->getMode() === AppState::MODE_DEVELOPER) {
            $this->assertSame('<!-- Source file "/does/not/exist.svg" does not exist -->', $output);
            return;
        }

        $this->assertSame('', $output);
    }

    private function getBlock(array $data = []): Template
    {
        $block = ObjectManager::getInstance()->get(LayoutInterface::class)->createBlock(Template::class, '', ['data' => $data]);
        $this->assertInstanceOf(Template::class, $block);

        return $block;
    }

    private function getImageRenderer(): ImageRenderer
    {
        return ObjectManager::getInstance()->create(ImageRenderer::class);
    }
}
