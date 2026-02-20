<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\Ajax;
use Loki\Components\Util\IsEmpty;
use Magento\Framework\App\RequestInterface;

class RequiredValidator implements ValidatorInterface
{
    public function __construct(
        private readonly IsEmpty $isEmpty,
        private readonly RequestInterface $request,
        private readonly Ajax $ajax,
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (false === $this->ajax->isAjax()) {
            return true;
        }

        if (true === $this->request->getParam('skip_validation')) {
            return true; // @todo: Set this flag in an internal place only
        }

        if ($this->isEmpty->execute($component, $value)) {
            return [(string)__('Value is required')];
        }

        return true;
    }
}
