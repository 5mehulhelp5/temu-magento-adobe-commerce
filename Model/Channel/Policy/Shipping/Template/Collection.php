<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Policy\Shipping\Template;

use M2E\Temu\Model\Channel\Policy\Shipping\Template;

class Collection
{
    /** @var \M2E\Temu\Model\Channel\Policy\Shipping\Template[] */
    private array $deliveryTemplates = [];

    public function add(\M2E\Temu\Model\Channel\Policy\Shipping\Template $deliveryTemplate): self
    {
        $this->deliveryTemplates[$deliveryTemplate->id] = $deliveryTemplate;

        return $this;
    }

    public function has(?string $id): bool
    {
        return isset($this->deliveryTemplates[$id]);
    }

    public function get(string $id): \M2E\Temu\Model\Channel\Policy\Shipping\Template
    {
        return $this->deliveryTemplates[$id];
    }

    public function isEmpty(): bool
    {
        return empty($this->deliveryTemplates);
    }

    /**
     * @return \M2E\Temu\Model\Channel\Policy\Shipping\Template[]
     */
    public function getAll(): array
    {
        return array_values($this->deliveryTemplates);
    }

    // ----------------------------------------

    public static function createFromArray(array $data): self
    {
        $obj = new self();
        foreach ($data as $deliveryTemplate) {
            $obj->add(Template::createFromArray($deliveryTemplate));
        }

        return $obj;
    }

    public function toArray(): array
    {
        $deliveryTemplates = [];
        foreach ($this->deliveryTemplates as $deliveryTemplate) {
            $deliveryTemplates[] = $deliveryTemplate->toArray();
        }

        return $deliveryTemplates;
    }
}
