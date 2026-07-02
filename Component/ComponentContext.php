<?php
declare(strict_types=1);

namespace Loki\Components\Component;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlFactory;

/**
 * @method CustomerSession getCustomerSession()
 * @method UrlFactory getUrlFactory()
 */
class ComponentContext extends AbstractComponentContext
{
    public function __construct(
        protected array $dependencies = []
    ) {
        parent::__construct($dependencies);
    }
}
