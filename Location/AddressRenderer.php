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

    public function getLine(Address $address): string
    {
        $renderer = $this->getRenderer('oneline');
        $text = $renderer->renderArray($this->getAddressDataFromAddress($address));
        $newTextParts = [];
        $textParts = explode(',', $text);
        foreach ($textParts as $textPart) {
            $textPart = trim($textPart);
            if (!empty($textPart)) {
                $newTextParts[] = $textPart;
            }
        }

        return implode(', ', $newTextParts);
    }

    public function getHtml(Address $address): string
    {
        $renderer = $this->getRenderer('html');
        $html = $renderer->renderArray($this->getAddressDataFromAddress($address));
        $html = preg_replace('/^<br(\s+)\/>$/msi', '', $html);
        return $html;
    }

    private function getRenderer(string $code = 'html'): RendererInterface
    {
        return $this->addressConfig->getFormatByCode($code)->getRenderer();
    }

    private function getAddressDataFromAddress(Address $address): array
    {
        $data = [
            'prefix' => $address->getPrefix(),
            'firstname' => $address->getFirstname(),
            'middlename' => $address->getMiddlename(),
            'lastname' => $address->getLastname(),
            'suffix' => $address->getSuffix(),
            'company' => $address->getCompany(),
            'street' => [$address->getStreet(), $address->getHouseNumber(), $address->getHouseNumberAddition()],
            'postcode' => $address->getPostcode(),
            'city' => $address->getCity(),
            'region' => $address->getRegion(),
            'country_id' => $address->getCountryId(),
        ];

        return $data;
    }
}
