<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary\Attribute;

class Serializer
{
    private \M2E\Temu\Model\Category\Dictionary\AttributeFactory $attributeFactory;

    public function __construct(
        \M2E\Temu\Model\Category\Dictionary\AttributeFactory $attributeFactory
    ) {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param SalesAttribute[] $attributes
     */
    public function serializeSalesAttributes(array $attributes): string
    {
        $data = [];
        foreach ($attributes as $attribute) {
            if (!$attribute instanceof SalesAttribute) {
                throw new \LogicException('Invalid attribute instance');
            }

            $data[] = [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'is_sale' => $attribute->isSale(),
                'is_required' => $attribute->isRequired(),
                'is_customized' => $attribute->isCustomised(),
                'is_multiple_selected' => $attribute->isMultipleSelected(),
                'type_format' => $attribute->getTypeFormat(),
                'rules' => $attribute->getRules(),
                'pid' => $attribute->getPid(),
                'ref_pid' => $attribute->getRefPid(),
                'template_pid' => $attribute->getTemplatePid(),
                'parent_spec_id' => $attribute->getParentSpecId()
            ];
        }

        return json_encode($data);
    }

    /**
     * @return SalesAttribute[]
     */
    public function unSerializeSalesAttributes(string $jsonAttributes): array
    {
        $attributes = [];
        foreach (json_decode($jsonAttributes, true) as $item) {
            $attributes[] = $this->attributeFactory->createSalesAttribute(
                (string)$item['id'],
                $item['name'],
                $item['is_sale'],
                $item['is_required'],
                $item['is_customized'],
                $item['is_multiple_selected'],
                $item['type_format'],
                $item['rules'],
                $item['pid'],
                $item['ref_pid'],
                $item['template_pid'],
                $item['parent_spec_id']
            );
        }

        return $attributes;
    }

    /**
     * @param ProductAttribute[] $attributes
     *
     * @return string
     */
    public function serializeProductAttributes(array $attributes): string
    {
        $data = [];
        foreach ($attributes as $attribute) {
            if (!$attribute instanceof ProductAttribute) {
                throw new \LogicException('Invalid attribute instance');
            }

            $values = [];
            foreach ($attribute->getValues() as $value) {
                $values[] = [
                    'id' => $value->getId(),
                    'name' => $value->getName(),
                    'spec_id' => $value->getSpecId(),
                    'group_id' => $value->getGroupId()
                ];
            }

            $data[] = [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'is_sale' => $attribute->isSale(),
                'is_required' => $attribute->isRequired(),
                'is_customized' => $attribute->isCustomised(),
                'is_multiple_selected' => $attribute->isMultipleSelected(),
                'type_format' => $attribute->getTypeFormat(),
                'rules' => $attribute->getRules(),
                'pid' => $attribute->getPid(),
                'ref_pid' => $attribute->getRefPid(),
                'template_pid' => $attribute->getTemplatePid(),
                'parent_spec_id' => $attribute->getParentSpecId(),
                'values' => $values
            ];
        }

        return json_encode($data);
    }

    /**
     * @return SalesAttribute[]
     */
    public function unSerializeProductAttributes(string $jsonAttributes): array
    {
        $attributes = [];
        foreach (json_decode($jsonAttributes, true) as $item) {
            $values = [];
            foreach ($item['values'] as $value) {
                $values[] = $this->attributeFactory->createValue(
                    $value['id'],
                    $value['name'],
                    $value['spec_id'],
                    $value['group_id']
                );
            }

            $attributes[] = $this->attributeFactory->createProductAttribute(
                (string)$item['id'],
                $item['name'],
                $item['is_sale'],
                $item['is_required'],
                $item['is_customized'],
                $item['is_multiple_selected'],
                $item['type_format'],
                $item['rules'],
                $item['pid'],
                $item['ref_pid'],
                $item['template_pid'],
                $item['parent_spec_id'],
                $values
            );
        }

        return $attributes;
    }
}
