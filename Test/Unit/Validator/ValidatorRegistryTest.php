<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Loki\Components\Validator\ValidatorInterface;
use Loki\Components\Validator\ValidatorRegistry;

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
