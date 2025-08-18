<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertStoreConfigValueEquals;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Test\Integration\Traits\AssertComponentErrors;
use Loki\Components\Test\Integration\Traits\CreateComponent;
use Loki\Components\Test\Integration\Traits\GetComponent;
use Loki\Components\Util\IdConvertor;
use Loki\Components\Validator\Validator;

class LokiComponentsTestCase extends AbstractController
{
    protected ?ObjectManagerInterface $objectManager = null;
    protected ?DataFixtureStorage $fixtures = null;

    use AssertStoreConfigValueEquals;
    use AssertModuleIsEnabled;

    use GetComponent;
    use CreateComponent;
    use AssertComponentErrors;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->fixtures = DataFixtureStorageManager::getStorage();

        $this->assertModuleIsEnabled('Loki_Components');
    }

    protected function getComponentByBlockName(string $blockName
    ): ComponentInterface {
        $layout = $this->getObjectManager()->get(LayoutInterface::class);
        $block = $layout->getBlock($blockName);
        $this->assertInstanceOf(Template::class, $block);

        $component = $block->getData('component');
        $this->assertInstanceOf(ComponentInterface::class, $component);

        return $component;
    }

    protected function assertComponentExistsOnPage(string $componentName,
        bool $debug = false
    ) {
        $component = $this->getComponent($componentName);
        $found = $this->existsComponentOnPage($component);
        $msg = 'Component "' . $component->getName() . '" not found';

        if ($debug) {
            $msg .= "\n" . $this->getResponseBody();
        }

        $this->assertTrue($found, $msg);
    }

    protected function assertComponentNotExistsOnPage(string $componentName,
        bool $debug = false
    ) {
        $component = $this->getComponent($componentName);
        $found = $this->existsComponentOnPage($component);
        $msg = 'Component "' . $component->getName() . '" found anyway';
        if ($debug) {
            $msg .= "\n" . $this->getResponseBody();
        }

        $this->assertFalse($found, $msg);
    }

    protected function assertElementIdExistsOnPage(string $elementId,
        bool $debug = false
    ) {
        $found = $this->existsElementIdOnPage($elementId);
        $msg = 'Element ID "' . $elementId . '" not found';
        if ($debug) {
            $msg .= "\n" . $this->getResponseBody();
        }

        $this->assertTrue($found, $msg);
    }

    protected function assertElementIdNotExistsOnPage(string $elementId,
        bool $debug = false
    ) {
        $found = $this->existsElementIdOnPage($elementId);
        $msg = 'Element ID "' . $elementId . '" found anyway';
        if ($debug) {
            $msg .= "\n" . $this->getResponseBody();
        }

        $this->assertFalse($found, $msg);
    }

    protected function assertComponentValidators(array $expectedValidators,
        ComponentInterface $component
    ) {
        foreach ($expectedValidators as $expectedValidator) {
            $this->assertContains(
                $expectedValidator, $component->getValidators()
            );
        }
    }

    protected function assertComponentNoValidators(array $expectedValidators,
        ComponentInterface $component
    ) {
        foreach ($expectedValidators as $expectedValidator) {
            $this->assertNotContains(
                $expectedValidator, $component->getValidators()
            );
        }
    }

    protected function assertComponentValidation(
        bool $expectedResult,
        array $expectedErrors,
        ComponentInterface $component,
        mixed $value,
    ) {
        $this->setAsAjaxRequest();
        $result = $this->getValidator()->validate($component, $value);

        if (true === $expectedResult) {
            $this->assertTrue(
                $result, 'Validation did not succeed for value: ' . $value
            );
        } else {
            $this->assertFalse(
                $result, 'Validation did not fail for value: ' . $value
            );
        }

        if (empty($expectedErrors)) {
            $this->assertComponentHasNoErrors($component);
        }

        foreach ($expectedErrors as $expectedError) {
            $this->assertComponentHasError($component, $expectedError);
        }
    }

    protected function existsElementIdOnPage(string $elementId): bool
    {
        $body = $this->getResponseBody();
        if (false === str_contains($body, ' id="' . $elementId . '"')) {
            return false;
        }

        echo 'Not rendered: ' . $elementId;
        if (str_contains($body, 'Not rendered: ' . $elementId)) {
            return false;
        }

        return true;
    }

    protected function assertStringNotOccursOnPage(string $string): void
    {
        $this->assertStringNotContainsString($string, $this->getResponseBody());
    }

    protected function assertStringOccursOnPage(string $string): void
    {
        $this->assertStringContainsString($string, $this->getResponseBody());
    }

    protected function existsComponentOnPage(ComponentInterface $component
    ): bool {
        $elementId = $this->getObjectManager()->get(IdConvertor::class)
            ->toElementId($component->getName());
        $body = $this->getResponseBody();
        if (false === str_contains($body, ' id="' . $elementId . '"')) {
            return false;
        }

        if (str_contains(
            $body, 'Not rendered: ' . $component->getName() . ' '
        )
        ) {
            return false;
        }

        return true;
    }

    protected function setAsAjaxRequest(): void
    {
        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));
        $this->getObjectManager()->get(HttpRequest::class)->setHeaders(
            $headers
        );

    }

    protected function getValidator(): Validator
    {
        return $this->getObjectManager()->get(Validator::class);
    }

    protected function getFilter(): Filter
    {
        return $this->getObjectManager()->get(Filter::class);
    }

    protected function getResponseBody(): string
    {
        /** @var Http $response */
        $response = $this->getResponse();
        return $response->getBody();
    }
}
