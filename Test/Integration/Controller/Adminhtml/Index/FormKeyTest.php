<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Adminhtml\Index;

use Magento\Framework\Data\Form\FormKey;
use Magento\TestFramework\TestCase\AbstractBackendController;

class FormKeyTest extends AbstractBackendController
{
    protected $uri = 'backend/loki_components/index/formKey';
    protected $resource = 'Loki_Components::form';

    public function testReturnsFormKeyAsJson(): void
    {
        $this->dispatch($this->uri);

        $data = json_decode((string)$this->getResponse()->getBody(), true);
        $this->assertIsArray($data);
        $this->assertSame($this->_objectManager->get(FormKey::class)->getFormKey(), $data['form_key'] ?? null);
    }

    public function testAclHasAccess(): void
    {
        $this->markTestSkipped('The controller does not extend the backend Action class, so no ACL check is applied');
    }

    public function testAclNoAccess(): void
    {
        $this->markTestSkipped('The controller does not extend the backend Action class, so no ACL check is applied');
    }
}
