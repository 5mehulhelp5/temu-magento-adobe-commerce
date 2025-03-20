<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Policy\Shipping;

class Template
{
    public string $id;
    public string $name;

    public function __construct(
        string $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
