<?php
declare(strict_types=1);

namespace Loki\Components\Component;

/**
 * @method \Magento\Customer\Model\Session getCustomerSession()
 * @method \Magento\Framework\UrlFactory getUrlFactory()
 */
class ComponentContext extends AbstractComponentContext
{
    public function __construct(
        protected array $dependencies = []
    ) {
        parent::__construct($dependencies);
    }
}
