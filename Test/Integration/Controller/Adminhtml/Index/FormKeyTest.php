<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Adminhtml\Index;

use Magento\Framework\Data\Form\FormKey;
use Magento\TestFramework\TestCase\AbstractBackendController;

class FormKeyTest extends AbstractBackendController
{
    protected $uri = 'backend/loki_components/index/formKey';
    protected $resource = 'Magento_Backend::admin';

    public function testReturnsFormKeyAsJson(): void
    {
        $this->dispatch($this->uri);

        $data = json_decode((string)$this->getResponse()->getBody(), true);
        $this->assertIsArray($data);
        $this->assertSame($this->_objectManager->get(FormKey::class)->getFormKey(), $data['form_key'] ?? null);
    }

    public function testDoesNotReturnFormKeyWithoutAdminLogin(): void
    {
        $this->_auth->logout();
        $this->dispatch($this->uri);

        $data = json_decode((string)$this->getResponse()->getBody(), true);
        $this->assertArrayNotHasKey('form_key', (array)$data, 'Anonymous visitors get an admin form key');
    }
}
