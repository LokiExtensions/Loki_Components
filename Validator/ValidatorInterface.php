<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;

interface ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): true|array;
}
