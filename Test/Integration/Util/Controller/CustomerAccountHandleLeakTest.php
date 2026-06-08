<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Components\Util\Controller\TargetRenderer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Test\Fixture\CustomerWithAddresses;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;

/**
 * Security regression guard for layout-handle injection through the AJAX component update.
 *
 * The frontend endpoint loki_components/index/html forwards request-supplied layout handles
 * straight into the layout (see Controller\Index\Html, RequestDataLoader and LayoutLoader),
 * without any allow-list. This test asserts that injecting the "customer_account" handle does
 * not leak a logged-in customer's sensitive details (email, name, address) into the rendered
 * AJAX response.
 */
#[AppArea('frontend')]
class CustomerAccountHandleLeakTest extends TestCase
{
    use AssertModuleIsRegistered;
    use AssertModuleIsEnabled;

    private const SENSITIVE_EMAIL = 'leak-test@example.com';
    private const SENSITIVE_FIRSTNAME = 'LeakFirstname';
    private const SENSITIVE_LASTNAME = 'LeakLastname';

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
    public function testCustomerAccountHandleDoesNotLeakCustomerDetails(): void
    {
        $this->assertModuleIsRegistered('Loki_Components');
        $this->assertModuleIsEnabled('Loki_Components');

        $customer = DataFixtureStorageManager::getStorage()->get('customer');
        $customerId = (int)$customer->getId();
        $this->assertGreaterThan(0, $customerId, 'Customer fixture was not created');

        $this->getCustomerSession()->loginById($customerId);

        $html = $this->renderHandle(
            ['customer_account', 'customer_account_index'],
            [
                'customer_account_dashboard_info',
                'customer_account_dashboard_address',
                'customer_account_navigation',
            ]
        );

        $this->assertStringContainsString(
            'block-dashboard-addresses',
            $html,
            'The "customer_account" dashboard address block did not render, so the no-leak assertions would be meaningless'
        );

        foreach ($this->getSensitiveValues($customerId) as $label => $value) {
            $this->assertStringNotContainsString(
                $value,
                $html,
                'Sensitive customer detail leaked via the "customer_account" layout handle: '.$label
            );
        }
    }

    /**
     * Collect the customer details that must never leak into an AJAX response.
     *
     * Both the dashboard "info" block (email and name from the logged-in customer) and the
     * dashboard "address" block (default billing/shipping address from storage) expose
     * personal data once the matching layout handles are applied.
     *
     * @return array<string, string>
     */
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

    private function renderHandle(array $handles, array $targets): string
    {
        $layout = $this->getLayoutLoader()->load($handles, [], true);
        $htmlParts = $this->getTargetRenderer()->render($layout, $targets, true);

        return implode("\n", $htmlParts);
    }

    private function getLayoutLoader(): LayoutLoader
    {
        return ObjectManager::getInstance()->get(LayoutLoader::class);
    }

    private function getTargetRenderer(): TargetRenderer
    {
        return ObjectManager::getInstance()->get(TargetRenderer::class);
    }

    private function getCustomerSession(): CustomerSession
    {
        return ObjectManager::getInstance()->get(CustomerSession::class);
    }

    private function getCustomerRepository(): CustomerRepositoryInterface
    {
        return ObjectManager::getInstance()->get(CustomerRepositoryInterface::class);
    }
}
