<?php declare(strict_types=1);

namespace Loki\Components\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Data\Form\FormKey as FormKeyData;

class FormKey extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Magento_Backend::admin';

    public function __construct(
        Context $context,
        private ResultJsonFactory $resultJsonFactory,
        private FormKeyData $formKey,
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultJson
    {
        /** @var ResultJson $result */
        $result = $this->resultJsonFactory->create();
        $result->setData(['form_key' => $this->formKey->getFormKey()]);

        return $result;
    }
}
