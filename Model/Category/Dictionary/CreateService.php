<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary;

class CreateService
{
    private \M2E\Temu\Model\Category\Tree\Repository $categoryTreeRepository;
    private \M2E\Temu\Model\Category\DictionaryFactory $dictionaryFactory;
    private \M2E\Temu\Model\Category\Tree\PathBuilder $pathBuilder;
    private \M2E\Temu\Model\Category\Dictionary\AttributeService $attributeService;
    private \M2E\Temu\Model\Category\Dictionary\Repository $categoryDictionaryRepository;

    public function __construct(
        \M2E\Temu\Model\Category\DictionaryFactory $dictionaryFactory,
        \M2E\Temu\Model\Category\Dictionary\AttributeService $attributeService,
        \M2E\Temu\Model\Category\Dictionary\Repository $categoryDictionaryRepository,
        \M2E\Temu\Model\Category\Tree\Repository $categoryTreeRepository,
        \M2E\Temu\Model\Category\Tree\PathBuilder $pathBuilder
    ) {
        $this->dictionaryFactory = $dictionaryFactory;
        $this->attributeService = $attributeService;
        $this->categoryDictionaryRepository = $categoryDictionaryRepository;
        $this->pathBuilder = $pathBuilder;
        $this->categoryTreeRepository = $categoryTreeRepository;
    }

    public function create(
        string $region,
        int $categoryId
    ): \M2E\Temu\Model\Category\Dictionary {
        $treeNode = $this->categoryTreeRepository
            ->getCategoryByRegionAndCategoryId($region, $categoryId);

        if ($treeNode === null) {
            throw new \M2E\Temu\Model\Exception\Logic('Not found category tree');
        }

        $categoryData = $this->attributeService->getCategoryDataFromServer($region, $categoryId);
        //$authorizedBrandData = $this->attributeService->getBrandsDataFromServer($region, $categoryId);//TODO brand

        $productAttributes = $this->attributeService->getProductAttributes($categoryData);
        $salesAttributes = $this->attributeService->getSalesAttributes($categoryData);
        $totalProductAttributes = $this->attributeService->getTotalProductAttributes($categoryData);
        $totalSalesAttributes = $this->attributeService->getTotalSalesAttributes($categoryData);
        $hasRequiredProductAttributes = $this->attributeService->getHasRequiredAttributes($categoryData);
        $hasRequiredSalesAttributes = $this->attributeService->getHasRequiredSalesAttributes($categoryData);

        $dictionary = $this->dictionaryFactory->create()->create(
            $region,
            $categoryId,
            $this->pathBuilder->getPath($treeNode),
            $salesAttributes,
            $productAttributes,
            $categoryData->getRules(),
            [], // TODO $authorizedBrandData->getBrands(),
            $totalProductAttributes,
            $hasRequiredProductAttributes,
            $totalSalesAttributes,
            $hasRequiredSalesAttributes
        );

        $this->categoryDictionaryRepository->create($dictionary);

        return $dictionary;
    }
}
