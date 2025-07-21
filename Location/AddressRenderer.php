<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Magento\Customer\Block\Address\Renderer\RendererInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;

class AddressRenderer
{
    public function __construct(
        private readonly AddressConfig $addressConfig,
    ) {
    }

    public function getHtml(Address $address): string
    {
        $addressData = [
            'street' => [$address->getStreet(), $address->getHouseNumber()],
            'postcode' => $address->getPostcode(),
            'city' => $address->getCity(),
            'country_id' => $address->getCountryId(),
        ];

        /** @var RendererInterface $renderer */
        $renderer = $this->addressConfig->getFormatByCode('html')->getRenderer();
        $html = $renderer->renderArray($addressData);
        $html = preg_replace('/^<br(\s+)\/>$/msi', '', $html);
        return $html;
    }
}
