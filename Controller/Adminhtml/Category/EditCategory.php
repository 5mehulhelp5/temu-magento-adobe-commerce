<?php

namespace M2E\Temu\Controller\Adminhtml\Category;

class EditCategory extends \M2E\Temu\Controller\Adminhtml\AbstractCategory
{
    private \M2E\Temu\Block\Adminhtml\Template\Category\ViewFactory $viewFactory;
    private \M2E\Temu\Model\Category\Dictionary\Manager $dictionaryManager;

    public function __construct(
        \M2E\Temu\Block\Adminhtml\Template\Category\ViewFactory $viewFactory,
        \M2E\Temu\Model\Category\Dictionary\Manager $dictionaryManager
    ) {
        parent::__construct();

        $this->viewFactory = $viewFactory;
        $this->dictionaryManager = $dictionaryManager;
    }

    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        $region = $this->getRequest()->getParam('region');
        $dictionary = $this->dictionaryManager->getOrCreateDictionary($region, $categoryId);

        $block = $this->viewFactory->create($this->getLayout(), $dictionary);
        $this->addContent($block);
        $this->getResultPage()->getConfig()->getTitle()->prepend(__('Edit Category'));

        return $this->getResult();
    }
}
