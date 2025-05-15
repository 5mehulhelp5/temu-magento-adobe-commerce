<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary;

class UpdateService
{
    private \M2E\Temu\Model\Category\Dictionary\AttributeService $attributeService;
    private \M2E\Temu\Model\Category\Dictionary\Repository $categoryDictionaryRepository;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\Category\Dictionary\AttributeService $attributeService,
        \M2E\Temu\Model\Category\Dictionary\Repository $categoryDictionaryRepository,
        \M2E\Temu\Model\Account\Repository $accountRepository
    ) {
        $this->attributeService = $attributeService;
        $this->categoryDictionaryRepository = $categoryDictionaryRepository;
        $this->accountRepository = $accountRepository;
    }

    public function update(
        \M2E\Temu\Model\Category\Dictionary $dictionary
    ): void {
        $region = $dictionary->getRegion();
        $categoryId = $dictionary->getCategoryId();
        $account = $this->accountRepository->findFirstForRegion($region);
        if ($account === null) {
            return;
        }
        try {
            $categoryData = $this->attributeService->getCategoryDataFromServer(
                $region,
                (int)$categoryId,
                $account->getServerHash()
            );
            //$authorizedBrandData = $this->attributeService->getBrandsDataFromServer($region, $categoryId); //TODO: brands

            $productAttributes = $this->attributeService->getProductAttributes($categoryData);
            $salesAttributes = $this->attributeService->getSalesAttributes($categoryData);
            $totalProductAttributes = $this->attributeService->getTotalProductAttributes($categoryData);
            $totalSalesAttributes = $this->attributeService->getTotalSalesAttributes($categoryData);
            $hasRequiredProductAttributes = $this->attributeService->getHasRequiredAttributes($categoryData);
            $hasRequiredSalesAttributes = $this->attributeService->getHasRequiredSalesAttributes($categoryData);

            $dictionary->setAuthorizedBrands([]);
            $dictionary->setProductAttributes($productAttributes);
            $dictionary->setSalesAttributes($salesAttributes);
            $dictionary->setCategoryRules($categoryData->getRules());
            $dictionary->setTotalProductAttributes($totalProductAttributes);
            $dictionary->setTotalSalesAttributes($totalSalesAttributes);
            $dictionary->setHasRequiredProductAttributes($hasRequiredProductAttributes);
            $dictionary->setHasRequiredSalesAttributes($hasRequiredSalesAttributes);
            $dictionary->markCategoryAsValid();
        } catch (\M2E\Temu\Model\Exception\CategoryInvalid $exception) {
            $dictionary->markCategoryAsInvalid();
        }

        $this->categoryDictionaryRepository->save($dictionary);
    }
}
