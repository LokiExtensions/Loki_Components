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

    public function getComponentByBlockName(string $blockName): ComponentInterface
    {
        $layout = $this->getObjectManager()->get(LayoutInterface::class);
        $block = $layout->getBlock($blockName);
        $this->assertInstanceOf(Template::class, $block);

        $component = $block->getData('component');
        $this->assertInstanceOf(ComponentInterface::class, $component);

        return $component;
    }

    public function assertComponentExistsOnPage(ComponentInterface $component)
    {
        $blockName = $component->getName();
        $elementId = $this->getObjectManager()->get(IdConvertor::class)->toElementId($blockName);
        $this->assertElementIdExistsOnPage($elementId);
    }

    public function assertElementIdExistsOnPage(string $elementId)
    {
        $body = $this->getResponse()->getBody();
        preg_match_all('/ id="([^\"]+)"/', $body, $matches);
        $found = str_contains($body, '"'.$elementId.'"');
        $this->assertTrue($found, 'Element ID "'.$elementId.'" not found: '.implode("\n", $matches[1]));
    }

    public function assertComponentNotExistsOnPage(string $blockName)
    {
        $body = $this->getResponse()->getBody();
        $this->assertStringNotContainsString('x-title="'.$blockName.'"', $body);
    }

    protected function assertComponentValidators(array $expectedValidators, ComponentInterface $component)
    {
        foreach ($expectedValidators as $expectedValidator) {
            $this->assertContains($expectedValidator, $component->getValidators());
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

    protected function setAsAjaxRequest(): void
    {
        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));
        $this->getObjectManager()->get(HttpRequest::class)->setHeaders($headers);

    }

    public function getValidator(): Validator
    {
        return $this->getObjectManager()->get(Validator::class);
    }

    public function getFilter(): Filter
    {
        return $this->getObjectManager()->get(Filter::class);
    }
}
