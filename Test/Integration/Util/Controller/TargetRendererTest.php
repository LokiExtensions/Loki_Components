<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Loki\Components\Util\Controller\TargetRenderer;

#[AppArea('frontend')]
class TargetRendererTest extends TestCase
{
    public function testRender()
    {
        $resultPageFactory = ObjectManager::getInstance()->get(ResultPageFactory::class);
        $resultPage = $resultPageFactory->create();
        $resultPage->addHandle('default');
        $resultPage->addHandle('loki_components');

        $layout = $resultPage->getLayout();
        $block = $layout->getBlock('loki-components.global-messages');
        $this->assertInstanceOf(AbstractBlock::class, $block);

        $targetRenderer = ObjectManager::getInstance()->get(TargetRenderer::class);
        $htmlParts = $targetRenderer->render($layout, ['loki-components.global-messages']);

        $this->assertNotEmpty($htmlParts);
    }
}
