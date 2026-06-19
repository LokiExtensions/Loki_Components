<?php declare(strict_types=1);

namespace Loki\Components\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Data\Form\FormKey as FormKeyData;

class FormKey implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Loki_Components::form';

    public function __construct(
        private ResultJsonFactory $resultJsonFactory,
        private FormKeyData $formKey,
    ) {
    }

    public function execute(): ResultJson
    {
        /** @var ResultJson $result */
        $result = $this->resultJsonFactory->create();
        $result->setData(['form_key' => $this->formKey->getFormKey()]);

        return $result;
    }
}
