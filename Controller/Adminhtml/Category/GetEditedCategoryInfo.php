<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Category;

class GetEditedCategoryInfo extends \M2E\Temu\Controller\Adminhtml\AbstractCategory
{
    private \M2E\Temu\Model\Category\Dictionary\Manager $dictionaryManager;

    public function __construct(
        \M2E\Temu\Model\Category\Dictionary\Manager $dictionaryManager
    ) {
        parent::__construct();

        $this->dictionaryManager = $dictionaryManager;
    }

    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        $region = $this->getRequest()->getParam('region');

        if (empty($categoryId) || empty($region)) {
            throw new \M2E\Temu\Model\Exception\Logic('Invalid input');
        }

        try {
            $dictionary = $this->dictionaryManager->getOrCreateDictionary($region, (int)$categoryId);
        } catch (\Throwable $e) {
            $this->setJsonContent([
                'success' => false,
                'message' => $e->getMessage()
            ]);

            return $this->getResult();
        }

        $this->setJsonContent([
            'success' => true,
            'dictionary_id' => $dictionary->getId(),
            'is_all_required_attributes_filled' => $dictionary->isAllRequiredAttributesFilled(),
            'path' => $dictionary->getPath(),
            'value' => $dictionary->getCategoryId(),
        ]);

        return $this->getResult();
    }
}
