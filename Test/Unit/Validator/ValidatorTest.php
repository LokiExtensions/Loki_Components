<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Exception\RecursionException;
use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Components\Messages\LocalMessage;
use Loki\Components\Messages\LocalMessageRegistry;
use Loki\Components\Validator\Validator;
use Loki\Components\Validator\ValidatorInterface;
use Loki\Components\Validator\ValidatorRegistry;
use Magento\Framework\Phrase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private ?LocalMessageRegistry $localMessageRegistry = null;
    private (GlobalMessageRegistry&MockObject)|null $globalMessageRegistry = null;

    protected function setUp(): void
    {
        $this->localMessageRegistry = new LocalMessageRegistry();
        $this->globalMessageRegistry = $this->createMock(GlobalMessageRegistry::class);
    }

    public function testReturnsTrueAndMarksComponentAsValidated(): void
    {
        $component = $this->getComponent(['pass']);
        $component->expects($this->once())->method('setIsValidated')->with(true);

        $validator = $this->getValidator(['pass' => $this->getValidatorStub(true)]);
        $this->assertTrue($validator->validate($component, 'foo'));
        $this->assertFalse($this->localMessageRegistry->hasMessages());
    }

    public function testIgnoresValidatorsThatAreNotConfiguredForComponent(): void
    {
        $validator = $this->getValidator(['fail' => $this->getValidatorStub(['Failure'])]);
        $this->assertTrue($validator->validate($this->getComponent(['pass']), 'foo'));
    }

    public function testAddsStringMessageAsLocalError(): void
    {
        $validator = $this->getValidator(['fail' => $this->getValidatorStub(['Value is wrong'])]);
        $this->assertFalse($validator->validate($this->getComponent(['fail']), 'foo'));

        $messages = $this->localMessageRegistry->getMessages();
        $this->assertCount(1, $messages);
        $this->assertSame('Value is wrong', $messages[0]->getText());
        $this->assertSame(LocalMessage::TYPE_ERROR, $messages[0]->getType());
    }

    public function testAddsPhraseMessageAsLocalError(): void
    {
        $validator = $this->getValidator(['fail' => $this->getValidatorStub([new Phrase('Minimum is %1', [3])])]);
        $this->assertFalse($validator->validate($this->getComponent(['fail']), 'foo'));

        $messages = $this->localMessageRegistry->getMessages();
        $this->assertSame('Minimum is 3', $messages[0]->getText());
    }

    public function testAddsLocalMessageAsIs(): void
    {
        $localMessage = new LocalMessage('Just a warning', LocalMessage::TYPE_WARNING);
        $validator = $this->getValidator(['fail' => $this->getValidatorStub([$localMessage])]);
        $this->assertFalse($validator->validate($this->getComponent(['fail']), 'foo'));

        $this->assertSame([$localMessage], $this->localMessageRegistry->getMessages());
    }

    public function testAddsGlobalErrorWhenLocalMessagesAreNotDispatched(): void
    {
        $this->globalMessageRegistry->expects($this->once())->method('addError')->with('Value is wrong');

        $validator = $this->getValidator(['fail' => $this->getValidatorStub(['Value is wrong'])]);
        $this->assertFalse($validator->validate($this->getComponent(['fail'], false), 'foo'));
        $this->assertFalse($this->localMessageRegistry->hasMessages());
    }

    public function testStopsAtFirstFailingValidator(): void
    {
        $second = $this->createMock(ValidatorInterface::class);
        $second->expects($this->never())->method('validate');

        $validator = $this->getValidator([
            'first' => $this->getValidatorStub(['First failure']),
            'second' => $second,
        ]);

        $this->assertFalse($validator->validate($this->getComponent(['first', 'second']), 'foo'));
        $this->assertCount(1, $this->localMessageRegistry->getMessages());
    }

    public function testValidatesEveryArrayValue(): void
    {
        $failOnBar = new class implements ValidatorInterface {
            public array $values = [];

            public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
            {
                $this->values[] = $value;
                return $value === 'bar' ? ['No bar allowed'] : true;
            }
        };

        $validator = $this->getValidator(['custom' => $failOnBar]);
        $this->assertTrue($validator->validate($this->getComponent(['custom']), ['foo', ['baz']]));
        $this->assertSame(['foo', 'baz'], $failOnBar->values);

        $this->assertFalse($validator->validate($this->getComponent(['custom']), ['foo', 'bar']));
    }

    public function testThrowsRecursionExceptionWhenTooDeep(): void
    {
        $validator = new Validator(new ValidatorRegistry([]), 2);
        $this->expectException(RecursionException::class);
        $validator->validate($this->getComponent([]), [[['foo']]]);
    }

    private function getValidator(array $validators): Validator
    {
        return new Validator(new ValidatorRegistry($validators));
    }

    private function getValidatorStub(bool|array $result): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($result);

        return $validator;
    }

    private function getComponent(array $validators, bool $dispatchLocalMessages = true): ComponentInterface&MockObject
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getValidators')->willReturn($validators);
        $component->method('dispatchLocalMessages')->willReturn($dispatchLocalMessages);
        $component->method('getLocalMessageRegistry')->willReturn($this->localMessageRegistry);
        $component->method('getGlobalMessageRegistry')->willReturn($this->globalMessageRegistry);

        return $component;
    }
}
