<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Components\Util\Controller\TargetRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class TargetRendererTest extends TestCase
{
    public function testRender(): void
    {
        $om = ObjectManager::getInstance();
        $layoutLoader = $om->get(LayoutLoader::class);
        $layout = $layoutLoader->load(['loki_components'], true);

        /** @var TargetRenderer $targetRenderer */
        $targetRenderer = $om->get(TargetRenderer::class);
        $htmlParts = $targetRenderer->render($layout, ['loki-messages'], true);

        $this->assertNotEmpty($htmlParts);
    }
}
