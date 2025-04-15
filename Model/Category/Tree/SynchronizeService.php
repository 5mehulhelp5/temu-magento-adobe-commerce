<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Tree;

class SynchronizeService
{
    private \M2E\Temu\Model\Channel\Category\Processor $connectionProcessor;
    private \M2E\Temu\Model\Category\Tree\Repository $categoryTreeRepository;
    private \M2E\Temu\Model\Category\TreeFactory $categoryFactory;

    public function __construct(
        \M2E\Temu\Model\Channel\Category\Processor $connectionProcessor,
        \M2E\Temu\Model\Category\Tree\Repository $categoryTreeRepository,
        \M2E\Temu\Model\Category\TreeFactory $categoryFactory
    ) {
        $this->connectionProcessor = $connectionProcessor;
        $this->categoryTreeRepository = $categoryTreeRepository;
        $this->categoryFactory = $categoryFactory;
    }

    public function synchronize(string $region, ?int $categoryId = null): void
    {
        $response = $this->connectionProcessor->process($region, $categoryId);

        $categories = [];
        foreach ($response->getCategories() as $category) {
            $categories[] = $this->categoryFactory->create()->create(
                $region,
                $category->getId(),
                $category->getParentId(),
                $category->getTitle(),
                $category->isLeaf()
            );
        }

        $this->categoryTreeRepository->deleteByRegionAndParentCategoryId($region, $categoryId);
        $this->categoryTreeRepository->batchInsert($categories);
    }
}
