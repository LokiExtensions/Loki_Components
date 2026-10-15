<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util;

use Loki\Components\Util\ComponentUtil;
use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class ComponentUtilTest extends TestCase
{
    private const HANDLE = 'loki_components_index_test';
    private const BLOCK_NAME = 'loki-components.test.dummy1';

    public function testUrls(): void
    {
        $componentUtil = $this->getComponentUtil();
        $this->assertStringContainsString('/loki_components/index/html', $componentUtil->getPostUrl());
        $this->assertStringContainsString('/loki_components/index/polling', $componentUtil->getPollingUrl());
    }

    public function testElementIds(): void
    {
        $componentUtil = $this->getComponentUtil();
        $this->assertSame('loki-components-test-dummy1', $componentUtil->convertToElementId(self::BLOCK_NAME));
        $this->assertSame('loki-components-test-dummy1', $componentUtil->getElementIdByBlockName(self::BLOCK_NAME));
    }

    public function testFormKey(): void
    {
        $this->assertSame(
            ObjectManager::getInstance()->get(FormKey::class)->getFormKey(),
            $this->getComponentUtil()->getFormKey()
        );
    }

    #[AppIsolation(true)]
    public function testComponentUpdateDataContainsHandlesAndRequest(): void
    {
        $data = $this->getComponentUtil()->getComponentUpdateData($this->getBlock());

        $this->assertSame(['handles', 'pageHandles', 'request'], array_keys($data));
        $this->assertContains(self::HANDLE, $data['handles']);
        $this->assertIsArray($data['pageHandles']);
        $this->assertSame(
            ['params', 'route_name', 'module_name', 'controller_module', 'controller_name', 'action_name', 'request_uri'],
            array_keys($data['request'])
        );
    }

    #[AppIsolation(true)]
    public function testSignatureCanBeVerifiedByAjaxSignature(): void
    {
        $block = $this->getBlock();
        $componentUtil = $this->getComponentUtil();
        $ajaxSignature = ObjectManager::getInstance()->get(AjaxSignature::class);

        $data = $componentUtil->getComponentUpdateData($block);
        $signature = $componentUtil->getSignature($block);

        $this->assertTrue($ajaxSignature->verify($data, $signature));

        $data['handles'][] = 'customer_account';
        $this->assertFalse($ajaxSignature->verify($data, $signature));
    }

    private function getBlock(): AbstractBlock
    {
        $layout = ObjectManager::getInstance()->get(LayoutLoader::class)->load([self::HANDLE]);
        $block = $layout->getBlock(self::BLOCK_NAME);
        $this->assertInstanceOf(AbstractBlock::class, $block);

        return $block;
    }

    private function getComponentUtil(): ComponentUtil
    {
        return ObjectManager::getInstance()->get(ComponentUtil::class);
    }
}
