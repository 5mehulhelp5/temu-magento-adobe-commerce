<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Delete;

class ValidatorFactory extends \M2E\Temu\Model\Product\Action\Type\AbstractValidatorFactory
{
    protected function getValidatorClass(): string
    {
        return Validator::class;
    }
}
