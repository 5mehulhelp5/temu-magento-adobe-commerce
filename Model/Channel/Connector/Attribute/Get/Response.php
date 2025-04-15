<?php

namespace M2E\Temu\Model\Channel\Connector\Attribute\Get;

class Response
{
    /** @var \M2E\Temu\Model\Channel\Attribute\Item[] */
    private array $attributes;
    private array $rules;

    public function __construct(array $attributes, array $rules)
    {
        $this->rules = $rules;
        $this->attributes = $attributes;
    }

    /**
     * @return \M2E\Temu\Model\Channel\Attribute\Item[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
