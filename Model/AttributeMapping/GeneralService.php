<?php

declare(strict_types=1);

namespace M2E\Temu\Model\AttributeMapping;

class GeneralService
{
    public const MAPPING_TYPE = 'general';

    private \M2E\Core\Model\AttributeMapping\Adapter $attributeMappingAdapter;
    private \M2E\Core\Model\AttributeMapping\AdapterFactory $attributeMappingAdapterFactory;
    private \M2E\Temu\Model\Category\Attribute\Repository $categoryAttributeRepository;

    public function __construct(
        \M2E\Temu\Model\Category\Attribute\Repository   $categoryAttributeRepository,
        \M2E\Core\Model\AttributeMapping\AdapterFactory $attributeMappingAdapterFactory
    ) {
        $this->categoryAttributeRepository = $categoryAttributeRepository;
        $this->attributeMappingAdapterFactory = $attributeMappingAdapterFactory;
    }

    /**
     * @return \M2E\Core\Model\AttributeMapping\Pair[]
     */
    public function getAll(): array
    {
        return $this->getAdapter()->findByType(self::MAPPING_TYPE);
    }

    /**
     * @param \M2E\Temu\Model\Category\CategoryAttribute[] $attributes
     *
     * @return void
     */
    public function create(array $attributes): void
    {
        /** @var \M2E\Core\Model\AttributeMapping\Pair[] $mapping */
        $mapping = [];
        foreach ($attributes as $attribute) {
            if ($attribute->isSalesAttribute()) {
                continue;
            }

            if (empty($attribute->getCustomAttributeValue())) {
                continue;
            }

            $mapping[] = $this->getAdapter()->createPair(
                self::MAPPING_TYPE,
                $attribute->getAttributeName(),
                $attribute->getAttributeName(),
                $attribute->getCustomAttributeValue()
            );
        }

        $this->getAdapter()->create($mapping, self::MAPPING_TYPE);
    }

    public function update(array $generalAttributes): void
    {
        $attributes = [];
        foreach ($generalAttributes as $channelCode => $magentoCode) {
            $attributes[] = $this->getAdapter()->createPair(
                self::MAPPING_TYPE,
                (string)$channelCode,
                (string)$channelCode,
                $magentoCode
            );
        }

        $attributesMapping = $this->removeUnknownAttributes($attributes);

        $this->getAdapter()->update($attributesMapping, self::MAPPING_TYPE);
    }

    /**
     * @param \M2E\Core\Model\AttributeMapping\Pair[] $mappingPairs
     *
     * @return \M2E\Core\Model\AttributeMapping\Pair[]
     */
    private function removeUnknownAttributes(array $mappingPairs): array
    {
        $result = [];

        $knownAttributesNames = $this->categoryAttributeRepository->getAllCustomAttributesNames();
        $knownAttributesNames = array_flip($knownAttributesNames);

        $toRemove = [];
        foreach ($mappingPairs as $generalPair) {
            if (isset($knownAttributesNames[$generalPair->getChannelAttributeCode()])) {
                $result[] = $generalPair;

                continue;
            }

            $toRemove[] = $generalPair;
        }

        if (!empty($toRemove)) {
            $this->getAdapter()->removeByChannelCodes($toRemove);
        }

        return $result;
    }

    // ----------------------------------------

    private function getAdapter(): \M2E\Core\Model\AttributeMapping\Adapter
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->attributeMappingAdapter)) {
            $this->attributeMappingAdapter = $this->attributeMappingAdapterFactory->create(
                \M2E\Temu\Helper\Module::IDENTIFIER
            );
        }

        return $this->attributeMappingAdapter;
    }
}
