<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertStoreConfigValueEquals;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Filter\Filter;
use Yireo\LokiComponents\Test\Integration\Traits\AssertComponentErrors;
use Yireo\LokiComponents\Test\Integration\Traits\CreateComponent;
use Yireo\LokiComponents\Test\Integration\Traits\GetComponent;
use Yireo\LokiComponents\Util\IdConvertor;
use Yireo\LokiComponents\Validator\Validator;
use Magento\Framework\App\Request\Http as HttpRequest;

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

        $this->assertModuleIsEnabled('Yireo_LokiComponents');
    }

    protected function getComponentByBlockName(string $blockName): ComponentInterface
    {
        $layout = $this->getObjectManager()->get(LayoutInterface::class);
        $block = $layout->getBlock($blockName);
        $this->assertInstanceOf(Template::class, $block);

        $component = $block->getData('component');
        $this->assertInstanceOf(ComponentInterface::class, $component);

        return $component;
    }

    protected function assertComponentExistsOnPage(string|ComponentInterface $component)
    {
        if ($component instanceof ComponentInterface) {
            $component = $component->getName();
        }

        $elementId = $this->getObjectManager()->get(IdConvertor::class)->toElementId($component);
        $this->assertElementIdExistsOnPage($elementId);
    }

    protected function assertElementIdExistsOnPage(string $elementId)
    {
        $found = $this->existsElementIdOnPage($elementId);
        $this->assertTrue($found, 'Element ID "'.$elementId.'" not found');
    }

    protected function assertElementIdNotExistsOnPage(string $elementId)
    {
        $found = $this->existsElementIdOnPage($elementId);
        $this->assertFalse($found, 'Element ID "'.$elementId.'" found anyway');
    }

    protected function assertComponentNotExistsOnPage(string|ComponentInterface $component)
    {
        if ($component instanceof ComponentInterface) {
            $component = $component->getName();
        }

        $elementId = $this->getObjectManager()->get(IdConvertor::class)->toElementId($component);
        $this->assertElementIdNotExistsOnPage($elementId);
    }

    protected function assertComponentValidators(array $expectedValidators, ComponentInterface $component)
    {
        foreach ($expectedValidators as $expectedValidator) {
            $this->assertContains($expectedValidator, $component->getValidators());
        }
    }

    protected function assertComponentNoValidators(array $expectedValidators, ComponentInterface $component)
    {
        foreach ($expectedValidators as $expectedValidator) {
            $this->assertNotContains($expectedValidator, $component->getValidators());
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
            $this->assertTrue($result, 'Validation did not succeed for value: '.$value);
        } else {
            $this->assertFalse($result, 'Validation did not fail for value: '.$value);
        }

        if(empty($expectedErrors)) {
            $this->assertComponentHasNoErrors($component);
        }

        foreach ($expectedErrors as $expectedError) {
            $this->assertComponentHasError($component, $expectedError);
        }
    }

    protected function existsElementIdOnPage(string $elementId): bool
    {
        $body = $this->getResponse()->getBody();
        return str_contains($body, ' id="'.$elementId.'"');
    }


    protected function setAsAjaxRequest(): void
    {
        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));
        $this->getObjectManager()->get(HttpRequest::class)->setHeaders($headers);

    }

    protected function getValidator(): Validator
    {
        return $this->getObjectManager()->get(Validator::class);
    }

    protected function getFilter(): Filter
    {
        return $this->getObjectManager()->get(Filter::class);
    }
}
