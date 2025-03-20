<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary;

use M2E\Temu\Model\Category\Dictionary;

class Manager
{
    private \M2E\Temu\Model\Category\Dictionary\Repository $dictionaryRepository;
    private \M2E\Temu\Model\Category\Dictionary\CreateService $createService;

    public function __construct(
        \M2E\Temu\Model\Category\Dictionary\Repository $dictionaryRepository,
        \M2E\Temu\Model\Category\Dictionary\CreateService $createService
    ) {
        $this->dictionaryRepository = $dictionaryRepository;
        $this->createService = $createService;
    }

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function getOrCreateDictionary(string $region, int $categoryId): Dictionary
    {
        $entity = $this->dictionaryRepository->findByRegionAndCategoryId($region, $categoryId);
        if ($entity !== null) {
            return $entity;
        }

        return $this->createService->create($region, $categoryId);
    }
}
