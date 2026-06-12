<?php declare(strict_types=1);

namespace Loki\Components\Controller;

use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Data\Form\FormKey;

class HtmlResult extends Raw
{
    public function __construct(
        private readonly FormKey $formKey
    ) {
    }

    protected function render(HttpResponseInterface $response)
    {
        $response->setHeader('X-Magento-Form-Key', $this->formKey->getFormKey());
        $response->setHeader('Content-Type', 'text/html');

        return parent::render($response);
    }
}
