<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Loki\Components\Util\Controller\TargetRenderer;

#[AppArea('frontend')]
class TargetRendererTest extends TestCase
{
    public function testRender(): void
    {
        $om = ObjectManager::getInstance();

        $resultPage = $om->get(ResultPageFactory::class)->create();
        $resultPage->addHandle('loki_components');

        $layout = $resultPage->getLayout();

        /** @var TargetRenderer $targetRenderer */
        $targetRenderer = $om->get(TargetRenderer::class);
        $htmlParts = $targetRenderer->render($layout, ['loki-messages'], true);

        $this->assertNotEmpty($htmlParts);
    }
}
