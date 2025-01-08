<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Validator\ValidatorInterface;
use Yireo\LokiComponents\Validator\ValidatorRegistry;

class ValidatorRegistryTest extends TestCase
{
    public function testGetSelectedValidators()
    {
        $validator1 = $this->createMock(ValidatorInterface::class);
        $validator2 = $this->createMock(ValidatorInterface::class);

        $validatorRegistry = new ValidatorRegistry(['item1' => $validator1, 'item2' => $validator2]);
        $this->assertCount(2, $validatorRegistry->getApplicableValidators(['item1', 'item2', 'item3']));
    }
}
