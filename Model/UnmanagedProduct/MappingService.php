<?php

declare(strict_types=1);

namespace M2E\Temu\Model\UnmanagedProduct;

use M2E\Temu\Model\Magento\Product as ProductModel;

/**
 * @psalm-suppress UndefinedClass
 */
class MappingService
{
    private \Magento\Catalog\Model\ProductFactory $productFactory;
    private \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository;
    private \M2E\Temu\Model\Magento\ProductFactory $magentoProductFactory;
    private \M2E\Temu\Helper\Magento\Product $magentoProductHelper;
    private \M2E\Temu\Model\UnmanagedProduct\VariantSku\SalesAttributeFactory $salesAttributeFactory;

    public function __construct(
        \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \M2E\Temu\Model\Magento\ProductFactory $magentoProductFactory,
        \M2E\Temu\Helper\Magento\Product $magentoProductHelper,
        \M2E\Temu\Model\UnmanagedProduct\VariantSku\SalesAttributeFactory $salesAttributeFactory
    ) {
        $this->productFactory = $productFactory;
        $this->unmanagedRepository = $unmanagedRepository;
        $this->magentoProductFactory = $magentoProductFactory;
        $this->magentoProductHelper = $magentoProductHelper;
        $this->salesAttributeFactory = $salesAttributeFactory;
    }

    /**
     * @param \M2E\Temu\Model\UnmanagedProduct[] $unmanagedProducts
     *
     * @return bool
     * @throws \M2E\Temu\Model\Exception
     */
    public function autoMapUnmanagedProducts(array $unmanagedProducts): bool
    {
        $unmanagedProductsFiltered = array_filter($unmanagedProducts, function ($unmanaged) {
            return !$unmanaged->hasMagentoProductId();
        });

        if (empty($unmanagedProductsFiltered)) {
            return false;
        }

        $result = true;
        foreach ($unmanagedProductsFiltered as $unmanaged) {
            if (!$this->autoMapUnmanagedProduct($unmanaged)) {
                $result = false;
            }
        }

        return $result;
    }

    private function autoMapUnmanagedProduct(\M2E\Temu\Model\UnmanagedProduct $unmanaged): bool
    {
        if ($unmanaged->hasMagentoProductId()) {
            return false;
        }

        if (!$unmanaged->getAccount()->getUnmanagedListingSettings()->isMappingEnabled()) {
            return false;
        }

        $magentoProduct = $this->findMagentoProduct($unmanaged);
        if ($magentoProduct === null) {
            return false;
        }

        return $this->mapProduct($unmanaged, $magentoProduct);
    }

    // ----------------------------------------

    private function findMagentoProduct(
        \M2E\Temu\Model\UnmanagedProduct $unmanaged
    ): ?\Magento\Catalog\Model\Product {
        $mappingTypes = $unmanaged->getAccount()->getUnmanagedListingSettings()->getMappingTypesByPriority();

        foreach ($mappingTypes as $type) {
            $magentoProduct = $this->findMagentoProductIdByMappingType($type, $unmanaged);

            if ($magentoProduct === null) {
                continue;
            }

            if ($this->isProductTypeCompatible($unmanaged, $magentoProduct)) {
                return $magentoProduct;
            }
        }

        return null;
    }

    private function isProductTypeCompatible(
        \M2E\Temu\Model\UnmanagedProduct $unmanagedProduct,
        \Magento\Catalog\Model\Product $magentoProduct
    ): bool {
        if (
            $this->magentoProductHelper->isSimpleType($magentoProduct->getTypeId())
            && $unmanagedProduct->isSimple()
        ) {
            return true;
        }

        if (
            $this->magentoProductHelper->isConfigurableType($magentoProduct->getTypeId())
            && !$unmanagedProduct->isSimple()
        ) {
            return true;
        }

        return false;
    }

    private function findMagentoProductIdByMappingType(
        string $type,
        \M2E\Temu\Model\UnmanagedProduct $unmanaged
    ): ?\Magento\Catalog\Model\Product {
        switch ($type) {
            case \M2E\Temu\Model\Account\Settings\UnmanagedListings::MAPPING_TYPE_BY_SKU:
                return $this->findSkuMappedMagentoProductId($unmanaged);
            case \M2E\Temu\Model\Account\Settings\UnmanagedListings::MAPPING_TYPE_BY_TITLE:
                return $this->findTitleMappedMagentoProductId($unmanaged);
            default:
                return null;
        }
    }

    private function findSkuMappedMagentoProductId(
        \M2E\Temu\Model\UnmanagedProduct $unmanaged
    ): ?\Magento\Catalog\Model\Product {
        $temp = $unmanaged->getSku();

        if (empty($temp)) {
            return null;
        }

        $settings = $unmanaged->getAccount()->getUnmanagedListingSettings();

        if ($settings->isMappingBySkuModeByProductId()) {
            $productId = trim($unmanaged->getSku());

            if (!ctype_digit($productId) || (int)$productId <= 0) {
                return null;
            }

            $product = $this->productFactory->create()->load($productId);

            if (
                $product->getId()
                && $this->isMagentoProductTypeAllowed($product->getTypeId())
            ) {
                return $product;
            }

            return null;
        }

        $attributeCode = null;

        if ($settings->isMappingBySkuModeBySku()) {
            $attributeCode = 'sku';
        }

        if ($settings->isMappingBySkuModeByAttribute()) {
            $attributeCode = $settings->getMappingAttributeBySku();
        }

        if ($attributeCode === null) {
            return null;
        }

        $storeId = $unmanaged->getRelatedStoreId();
        $attributeValue = trim($unmanaged->getSku());

        $productObj = $this->productFactory->create()->setStoreId($storeId);
        $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

        if (
            $productObj instanceof \Magento\Catalog\Model\Product
            && $productObj->getId()
            && $this->isMagentoProductTypeAllowed($productObj->getTypeId())
        ) {
            return $productObj;
        }

        return null;
    }

    private function findTitleMappedMagentoProductId(
        \M2E\Temu\Model\UnmanagedProduct $unmanaged
    ): ?\Magento\Catalog\Model\Product {
        $temp = $unmanaged->getTitle();

        if (empty($temp)) {
            return null;
        }

        $settings = $unmanaged->getAccount()->getUnmanagedListingSettings();

        $attributeCode = null;

        if ($settings->isMappingByTitleModeByProductName()) {
            $attributeCode = 'name';
        }

        if ($settings->isMappingByTitleModeByAttribute()) {
            $attributeCode = $settings->getMappingAttributeByTitle();
        }

        if ($attributeCode === null) {
            return null;
        }

        $storeId = $unmanaged->getRelatedStoreId();
        $attributeValue = trim($unmanaged->getTitle());

        $productObj = $this->productFactory->create()->setStoreId($storeId);
        $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

        if (
            $productObj instanceof \Magento\Catalog\Model\Product
            && $productObj->getId()
            && $this->isMagentoProductTypeAllowed($productObj->getTypeId())
        ) {
            return $productObj;
        }

        return null;
    }

    private function isMagentoProductTypeAllowed($type): bool
    {
        $allowedTypes = [
            ProductModel::TYPE_SIMPLE_ORIGIN,
            ProductModel::TYPE_VIRTUAL_ORIGIN,
        ];

        return in_array($type, $allowedTypes);
    }

    // ----------------------------------------

    private function mapProduct(
        \M2E\Temu\Model\UnmanagedProduct $unmanagedProduct,
        \Magento\Catalog\Model\Product $magentoProduct
    ): bool {

        if (!$unmanagedProduct->isSimple()) {
            return false;
        }

        if ($unmanagedProduct->isSimple()) {
            $this->mapSimple($unmanagedProduct, $magentoProduct);

            return true;
        }

        $result = $this->mapVariants($unmanagedProduct, $magentoProduct);

        if ($result) {
            $unmanagedProduct->mapToMagentoProduct((int)$magentoProduct->getId());

            $this->unmanagedRepository->save($unmanagedProduct);
        }

        return $result;
    }

    private function mapSimple(
        \M2E\Temu\Model\UnmanagedProduct $unmanagedProduct,
        \Magento\Catalog\Model\Product $magentoProduct
    ): void {
        $magentoProductId = (int)$magentoProduct->getId();
        $unmanagedProduct->mapToMagentoProduct($magentoProductId);
        $this->unmanagedRepository->save($unmanagedProduct);

        $variant = $unmanagedProduct->getFirstVariant();
        $variant->mapToMagentoProduct($magentoProductId);

        $this->unmanagedRepository->saveVariant($variant);
    }

    public function mapVariants(
        \M2E\Temu\Model\UnmanagedProduct $unmanagedProduct,
        \Magento\Catalog\Model\Product $magentoProduct
    ): bool {
        $magentoVariants = $magentoProduct->getTypeInstance()->getUsedProducts($magentoProduct);
        $configurableAttributes = $magentoProduct->getTypeInstance()->getConfigurableAttributes($magentoProduct);

        $unmanagedVariants = $unmanagedProduct->getVariants();
        $unmanagedAttributeNames = $unmanagedProduct->getSalesAttributeNames();

        sort($unmanagedAttributeNames);
        $unmanagedSalesAttributeNames = $this->normalizeAttributeValue(
            implode('_', $unmanagedAttributeNames)
        );

        $attributeNames = [];
        foreach ($configurableAttributes as $attribute) {
            $attributeNames[] = $attribute->getProductAttribute()->getAttributeCode();
        }
        sort($attributeNames);

        $magentoAttributeNames = $this->normalizeAttributeValue(
            implode('_', $attributeNames)
        );

        if ($unmanagedSalesAttributeNames !== $magentoAttributeNames) {
            return false;
        }

        $variantsToSave = [];
        $magentoVariantMap = $this->createMagentoVariantMap($magentoVariants, $attributeNames);

        foreach ($unmanagedVariants as $unmanagedVariant) {
            $matchKey = $this->createMatchKey($unmanagedVariant->getSalesAttributes());
            if (isset($magentoVariantMap[$matchKey])) {
                $unmanagedVariant->mapToMagentoProduct((int)$magentoVariantMap[$matchKey]->getId());
                $variantsToSave[] = $unmanagedVariant;
            }
        }

        if (!empty($variantsToSave)) {
            $this->unmanagedRepository->saveVariants($variantsToSave);
        } else {
            return false;
        }

        return true;
    }

    private function createMagentoVariantMap(array $magentoVariants, array $attributeNames): array
    {
        $variantMap = [];
        foreach ($magentoVariants as $variant) {
            $attributesData = [];
            foreach ($attributeNames as $attrName) {
                $attributesData[] = $this->salesAttributeFactory->create([
                    'name' => $attrName,
                    'value_name' => $variant->getResource()
                                            ->getAttribute($attrName)
                                            ->getSource()
                                            ->getOptionText($variant->getData($attrName)),
                ]);
            }

            $key = $this->createMatchKey($attributesData);

            $variantMap[$key] = $variant;
        }

        return $variantMap;
    }

    /**
     * @param \M2E\Temu\Model\UnmanagedProduct\VariantSku\SalesAttribute[] $attributes
     *
     * @return string
     */
    private function createMatchKey(array $attributes): string
    {
        $normalizedAttributes = array_map(function ($attr) {
            $name = $this->normalizeAttributeValue($attr->getName());
            $value = $this->normalizeAttributeValue($attr->getValueName());

            return $name . ':' . $value;
        }, $attributes);
        sort($normalizedAttributes);
        return implode('|', $normalizedAttributes);
    }

    // ----------------------------------------

    public function manualMapProduct(int $unmanagedId, int $productId): bool
    {
        $unmanagedProduct = $this->unmanagedRepository->findById($unmanagedId);
        if (!$unmanagedProduct) {
            return false;
        }

        $magentoProduct = $this->magentoProductFactory->createByProductId($productId);

        return $this->mapProduct($unmanagedProduct, $magentoProduct->getProduct());
    }

    public function unmapProduct(\M2E\Temu\Model\UnmanagedProduct $product): void
    {
        $product->unmapFromMagentoProduct();
        $this->unmanagedRepository->save($product);
    }

    public function unmapVariants(array $variants): void
    {
        foreach ($variants as $variant) {
            $variant->unmapVariant();
            $this->unmanagedRepository->saveVariant($variant);
        }
    }

    private function normalizeAttributeValue(string $value): string
    {
        $preparedValue = strtolower(trim($value));

        return preg_replace('/\s+/', '_', $preparedValue);
    }
}
