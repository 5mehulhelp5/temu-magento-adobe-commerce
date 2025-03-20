<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary;

class AttributeService
{
    private \M2E\Temu\Model\Connector\Attribute\Get\Processor $attributeGetProcessor;
    private \M2E\Temu\Model\Connector\Brands\Get\Processor $brandGetProcessor;
    private \M2E\Temu\Model\Category\Dictionary\AttributeFactory $attributeFactory;

    public function __construct(
        \M2E\Temu\Model\Connector\Attribute\Get\Processor $attributeGetProcessor,
        \M2E\Temu\Model\Connector\Brands\Get\Processor $brandGetProcessor,
        \M2E\Temu\Model\Category\Dictionary\AttributeFactory $attributeFactory
    ) {
        $this->attributeGetProcessor = $attributeGetProcessor;
        $this->brandGetProcessor = $brandGetProcessor;
        $this->attributeFactory = $attributeFactory;
    }

    public function getCategoryDataFromServer(
        string $region,
        int $categoryId
    ): \M2E\Temu\Model\Connector\Attribute\Get\Response {
        return $this->attributeGetProcessor
            ->process($region, $categoryId);
    }

    /*
        public function getBrandsDataFromServer(
            \M2E\Temu\Model\Shop $shop,
            string $categoryId
        ): \M2E\Temu\Model\Connector\Brands\Get\Response {
            return $this->brandGetProcessor
                ->processAuthorizedBrands($shop->getAccount(), $shop, $categoryId);
        }
    */
    public function getProductAttributes(
        \M2E\Temu\Model\Connector\Attribute\Get\Response $categoryData
    ): array {
        $productAttributes = [];
        foreach ($categoryData->getAttributes() as $responseAttribute) {
            if ($responseAttribute->isSalesType()) {
                continue;
            }
            $values = [];
            foreach ($responseAttribute->getValues() as $value) {
                $values[] = $this->attributeFactory->createValue(
                    $value['id'],
                    $value['name'],
                    $value['spec_id'],
                    $value['group_id']
                );
            }

            $productAttributes[] = $this->attributeFactory->createProductAttribute(
                $responseAttribute->getId(),
                $responseAttribute->getName(),
                $responseAttribute->isSale(),
                $responseAttribute->isRequired(),
                $responseAttribute->isCustomised(),
                $responseAttribute->isMultipleSelected(),
                $responseAttribute->getTypeFormat(),
                $responseAttribute->getRules(),
                $responseAttribute->getPid(),
                $responseAttribute->getRefPid(),
                $responseAttribute->getTemplatePid(),
                $responseAttribute->getParentSpecId(),
                $values
            );
        }

        return $productAttributes;
    }

    public function getSalesAttributes(
        \M2E\Temu\Model\Connector\Attribute\Get\Response $categoryData
    ): array {
        $salesAttributes = [];
        foreach ($categoryData->getAttributes() as $responseAttribute) {
            if (!$responseAttribute->isSalesType()) {
                continue;
            }
            $salesAttributes[] = $this->attributeFactory->createSalesAttribute(
                $responseAttribute->getId(),
                $responseAttribute->getName(),
                $responseAttribute->isSale(),
                $responseAttribute->isRequired(),
                $responseAttribute->isCustomised(),
                $responseAttribute->isMultipleSelected(),
                $responseAttribute->getTypeFormat(),
                $responseAttribute->getRules(),
                $responseAttribute->getPid(),
                $responseAttribute->getRefPid(),
                $responseAttribute->getTemplatePid(),
                $responseAttribute->getParentSpecId()
            );
        }

        return $salesAttributes;
    }

    public function getTotalProductAttributes(
        \M2E\Temu\Model\Connector\Attribute\Get\Response $categoryData
    ): int {
        $productAttributesCount = 0;

        foreach ($categoryData->getAttributes() as $attribute) {
            if ($attribute->isProductType() || $attribute->isSalesType()) {
                $productAttributesCount++;
            }
        }

        //$productAttributesCount++; // +1 for brand attribute

        $categoryRules = $categoryData->getRules();

        // + size chart attribute
        if ($categoryRules['size_chart']['is_supported'] ?? false) {
            ++$productAttributesCount;
        }

        return $productAttributesCount;
    }

    public function getTotalSalesAttributes(
        \M2E\Temu\Model\Connector\Attribute\Get\Response $categoryData
    ): int {
        $salesAttributesCount = 0;

        foreach ($categoryData->getAttributes() as $attribute) {
            if ($attribute->isSalesType()) {
                $salesAttributesCount++;
            }
        }

        return $salesAttributesCount;
    }

    public function getHasRequiredAttributes(
        \M2E\Temu\Model\Connector\Attribute\Get\Response $categoryData
    ): bool {
        foreach ($categoryData->getAttributes() as $attribute) {
            if ($attribute->isProductType() && $attribute->isRequired()) {
                return true;
            }
        }

        return false;
    }

    public function getHasRequiredSalesAttributes(
        \M2E\Temu\Model\Connector\Attribute\Get\Response $categoryData
    ): bool {
        foreach ($categoryData->getAttributes() as $attribute) {
            if ($attribute->isSalesType() && $attribute->isRequired()) {
                return true;
            }
        }

        return false;
    }
}
