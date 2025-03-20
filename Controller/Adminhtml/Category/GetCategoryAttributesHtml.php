<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Category;

class GetCategoryAttributesHtml extends \M2E\Temu\Controller\Adminhtml\AbstractCategory
{
    private \M2E\Temu\Model\Category\Dictionary\Repository $dictionaryRepository;

    public function __construct(
        \M2E\Temu\Model\Category\Dictionary\Repository $dictionaryRepository
    ) {
        parent::__construct();

        $this->dictionaryRepository = $dictionaryRepository;
    }

    public function execute()
    {
        $dictionaryId = $this->getRequest()->getParam('dictionary_id');

        if (empty($dictionaryId)) {
            throw new \M2E\Temu\Model\Exception\Logic('Invalid input');
        }

        $dictionary = $this->dictionaryRepository->find((int)$dictionaryId);
        if ($dictionary === null) {
            throw new \M2E\Temu\Model\Exception\Logic('Dictionary not found');
        }

        /** @var \M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Specific\Edit $attributes */
        $attributes = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Specific\Edit::class,
            '',
            ['dictionary' => $dictionary]
        );

        $attributes->prepareFormData();
        $this->setAjaxContent($attributes->toHtml());

        return $this->getResult();
    }
}
