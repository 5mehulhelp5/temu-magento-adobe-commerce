<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category;

class CategoryTreeLoader
{
    private Tree\SynchronizeService $categoryTreeSynchronizeService;
    private Tree\Repository $categoryTreeRepository;

    public function __construct(
        \M2E\Temu\Model\Category\Tree\Repository         $categoryTreeRepository,
        \M2E\Temu\Model\Category\Tree\SynchronizeService $categoryTreeSynchronizeService
    ) {
        $this->categoryTreeSynchronizeService = $categoryTreeSynchronizeService;
        $this->categoryTreeRepository = $categoryTreeRepository;
    }

    /**
     * @param string $region
     * @param int|null $categoryId
     *
     * @return \M2E\Temu\Model\Category\Tree[]
     */
    public function getCategories(string $region, ?int $categoryId = null): array
    {
        if (!$this->categoryTreeRepository->categoryTreeExists($region, $categoryId)) {
            $this->categoryTreeSynchronizeService->synchronize($region, $categoryId);
        }

        if ($categoryId === null) {
            return $this->categoryTreeRepository->getRootCategories($region);
        }

        return $this->categoryTreeRepository->getChildCategories($region, $categoryId);
    }
}
