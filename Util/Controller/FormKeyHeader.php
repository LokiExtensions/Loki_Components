<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator;

class FormKeyHeader
{
    public const HEADER_NAME = 'X-Magento-Form-Key';

    public function __construct(
        private readonly Validator $formKeyValidator
    ) {
    }

    /**
     * Validate the form key that is sent via a header (or via the legacy "form_key" parameter)
     */
    public function validate(RequestInterface $request): bool
    {
        if ($request instanceof HttpRequest) {
            $formKey = $request->getHeader(self::HEADER_NAME);
            if (is_string($formKey) && $formKey !== '') {
                $request->setParam('form_key', $formKey);
            }
        }

        return (bool)$this->formKeyValidator->validate($request);
    }
}
