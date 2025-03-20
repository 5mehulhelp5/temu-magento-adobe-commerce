<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Template\Category;

/**
 * @method \M2E\Temu\Model\Category\Dictionary getModel()
 */
class SnapshotBuilder extends \M2E\Temu\Model\ActiveRecord\SnapshotBuilder
{
    public function getSnapshot(): array
    {
        $data = [];

        foreach ($this->getModel()->getRelatedAttributes() as $attribute) {
            $data[$attribute->getAttributeId()] = $this->makeAttributeHash($attribute);
        }

        ksort($data);

        return ['attributes' => json_encode($data, JSON_THROW_ON_ERROR)];
    }

    private function makeAttributeHash(\M2E\Temu\Model\Category\CategoryAttribute $attribute)
    {
        return json_encode([
            $attribute->getAttributeId(),
            $attribute->getAttributeName(),
            $attribute->getAttributeType(),
            $attribute->getValueMode(),
            $attribute->getRecommendedValue(),
            $attribute->getCustomValue(),
            $attribute->getCustomAttributeValue(),
        ], JSON_THROW_ON_ERROR);
    }
}
