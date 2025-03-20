<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category;

use M2E\Temu\Model\Category\Search\ResultCollection;
use M2E\Temu\Model\Category\Search\ResultItem;

class Search
{
    private \M2E\Temu\Model\Category\Tree\Repository $categoryRepository;
    private \M2E\Temu\Model\Category\Tree\PathBuilder $pathBuilder;
    private \M2E\Temu\Model\Category\Dictionary\Repository $dictionaryRepository;

    public function __construct(
        \M2E\Temu\Model\Category\Tree\Repository $categoryRepository,
        \M2E\Temu\Model\Category\Tree\PathBuilder $pathBuilder,
        \M2E\Temu\Model\Category\Dictionary\Repository $dictionaryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->pathBuilder = $pathBuilder;
        $this->dictionaryRepository = $dictionaryRepository;
    }

    public function process(string $region, string $searchQuery, int $limit): ResultCollection
    {
        $resultCollection = new ResultCollection($limit);
        $foundedItems = $this->categoryRepository->searchByTitleOrId($region, $searchQuery, $limit);
        if (count($foundedItems) === 0) {
            return $resultCollection;
        }

        foreach ($foundedItems as $item) {
            if ($item->isLeaf()) {
                $this->addLeafItem($resultCollection, $item);

                continue;
            }

            $this->addCategoryChildren($resultCollection, $item);
            if ($resultCollection->getCount() > $limit) {
                break;
            }
        }

        return $resultCollection;
    }

    private function addLeafItem(ResultCollection $resultCollection, Tree $treeItem): void
    {
        $resultCollection->add(
            new ResultItem(
                $treeItem->getCategoryId(),
                $this->pathBuilder->getPath($treeItem),
                $treeItem->isInviteOnly(), //will remove
                $this->isValidCategory($treeItem)
            )
        );
    }

    private function isValidCategory(Tree $treeItem): bool
    {
        $dictionary = $this->dictionaryRepository
            ->findByRegionAndCategoryId($treeItem->getRegion(), $treeItem->getCategoryId());

        if ($dictionary === null) {
            return true;
        }

        return $dictionary->isCategoryValid();
    }

    private function addCategoryChildren(ResultCollection $resultCollection, Tree $treeItem): void
    {
        $children = $this->categoryRepository
            ->getChildren($treeItem->getRegion(), $treeItem->getCategoryId());

        foreach ($children as $child) {
            $this->addLeafItem($resultCollection, $child);
        }
    }
}
