<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Loki\Components\Test\Integration\LokiComponentsTestCase;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Test\Fixture\CustomerWithAddresses;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use RuntimeException;

#[AppArea('frontend')]
class CustomerAccountHandleLeakTest extends LokiComponentsTestCase
{
    private const SENSITIVE_EMAIL = 'leak-test@example.com';
    private const SENSITIVE_FIRSTNAME = 'LeakFirstname';
    private const SENSITIVE_LASTNAME = 'LeakLastname';

    private const CUSTOMER_ACCOUNT_HANDLES = [
        'customer_account',
        'customer_account_index',
    ];

    private const CUSTOMER_ACCOUNT_TARGETS = [
        'customer_account_dashboard_info',
        'customer_account_dashboard_address',
        'customer_account_navigation',
    ];

    #[DbIsolation(true)]
    #[AppIsolation(true)]
    #[DataFixture(
        CustomerWithAddresses::class,
        [
            'email' => self::SENSITIVE_EMAIL,
            'firstname' => self::SENSITIVE_FIRSTNAME,
            'lastname' => self::SENSITIVE_LASTNAME,
        ],
        'customer'
    )]
    public function testTamperedCustomerAccountHandleIsRejected(): void
    {
        $customer = DataFixtureStorageManager::getStorage()->get('customer');
        $customerId = (int)$customer->getId();
        $this->assertGreaterThan(0, $customerId, 'Customer fixture was not created');

        $this->getCustomerSession()->loginById($customerId);

        $body = $this->buildSignedBody([], [], []);
        $body['handles'] = self::CUSTOMER_ACCOUNT_HANDLES;
        $body['targets'] = self::CUSTOMER_ACCOUNT_TARGETS;

        $exceptionMessage = '';
        try {
            $this->dispatchHtmlRequest($body);
        } catch (RuntimeException $exception) {
            $exceptionMessage = $exception->getMessage();
        }

        $responseBody = $this->getResponseBody();

        $this->assertTrue(
            $exceptionMessage === 'Payload was tampered with'
                || str_contains($responseBody, 'Payload was tampered with'),
            'Tampered request was not rejected by the signature check'
        );

        $this->assertStringNotContainsString(
            'block-dashboard-addresses',
            $responseBody,
            'The "customer_account" dashboard address block rendered for a tampered request'
        );

        foreach ($this->getSensitiveValues($customerId) as $label => $value) {
            $this->assertStringNotContainsString(
                $value,
                $responseBody,
                'Sensitive customer detail leaked via a tampered "customer_account" request: '.$label
            );
        }
    }

    #[DbIsolation(true)]
    #[AppIsolation(true)]
    #[DataFixture(
        CustomerWithAddresses::class,
        [
            'email' => self::SENSITIVE_EMAIL,
            'firstname' => self::SENSITIVE_FIRSTNAME,
            'lastname' => self::SENSITIVE_LASTNAME,
        ],
        'customer'
    )]
    public function testSignedCustomerAccountHandleLeaksWithoutSignatureProtection(): void
    {
        $customer = DataFixtureStorageManager::getStorage()->get('customer');
        $customerId = (int)$customer->getId();
        $this->assertGreaterThan(0, $customerId, 'Customer fixture was not created');

        $this->getCustomerSession()->loginById($customerId);

        $body = $this->buildSignedBody(
            self::CUSTOMER_ACCOUNT_HANDLES,
            [],
            self::CUSTOMER_ACCOUNT_TARGETS
        );

        $this->dispatchHtmlRequest($body);

        $responseBody = $this->getResponseBody();

        $this->assertStringContainsString(
            'block-dashboard-addresses',
            $responseBody,
            'The "customer_account" dashboard address block did not render, so this positive control is meaningless'
        );

        $this->assertStringContainsString(
            self::SENSITIVE_EMAIL,
            $responseBody,
            'Positive control failed: a validly signed "customer_account" request is expected to render '
            .'customer details, proving the signature check in testTamperedCustomerAccountHandleIsRejected '
            .'is the only thing preventing an attacker from reaching this output'
        );
    }

    private function dispatchHtmlRequest(array $body): void
    {
        $this->setAsAjaxRequest();

        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setContent((string)json_encode($body));

        $this->dispatch('loki_components/index/html');
    }

    private function buildSignedBody(array $handles, array $pageHandles, array $targets): array
    {
        $request = [];

        $body = [
            'updates' => [],
            'targets' => $targets,
            'handles' => $handles,
            'pageHandles' => $pageHandles,
            'request' => $request,
        ];

        $body['signature'] = $this->getAjaxSignature()->sign($handles, $pageHandles, $request);

        return $body;
    }

    private function getSensitiveValues(int $customerId): array
    {
        $values = [
            'email' => self::SENSITIVE_EMAIL,
        ];

        $customer = $this->getCustomerRepository()->getById($customerId);
        $defaultBillingId = $customer->getDefaultBilling();

        foreach ($customer->getAddresses() as $address) {
            if ((string)$address->getId() !== (string)$defaultBillingId) {
                continue;
            }

            $values['address_firstname'] = (string)$address->getFirstname();
            $values['address_lastname'] = (string)$address->getLastname();
            $values['city'] = (string)$address->getCity();
            $values['postcode'] = (string)$address->getPostcode();
            $values['telephone'] = (string)$address->getTelephone();

            $street = $address->getStreet();
            if (!empty($street[0])) {
                $values['street'] = (string)$street[0];
            }
        }

        return array_filter($values, static fn (string $value): bool => $value !== '');
    }

    private function getAjaxSignature(): AjaxSignature
    {
        return $this->getObjectManager()->get(AjaxSignature::class);
    }

    private function getCustomerSession(): CustomerSession
    {
        return $this->getObjectManager()->get(CustomerSession::class);
    }

    private function getCustomerRepository(): CustomerRepositoryInterface
    {
        return $this->getObjectManager()->get(CustomerRepositoryInterface::class);
    }
}
