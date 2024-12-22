<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Validator;

use Yireo\LokiComponents\Component\MutableComponentInterface;

interface ValidatorInterface
{
    public function validate(MutableComponentInterface $component): bool;
}
