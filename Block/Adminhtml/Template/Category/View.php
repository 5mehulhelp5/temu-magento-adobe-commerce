<?php

namespace M2E\Temu\Block\Adminhtml\Template\Category;

use M2E\Temu\Helper\Module;

class View extends \M2E\Temu\Block\Adminhtml\Magento\AbstractContainer
{
    private \M2E\Temu\Model\Category\Dictionary $dictionary;

    public function __construct(
        \M2E\Temu\Model\Category\Dictionary $dictionary,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        array $data = []
    ) {
        $this->dictionary = $dictionary;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('temuCategoryView');
        $this->_template = Module::IDENTIFIER . '::category/view.phtml';

        $this->removeButton('back');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');
    }

    protected function _prepareLayout()
    {
        /** @var \M2E\Temu\Block\Adminhtml\Template\Category\View\Info $infoBlock */
        $infoBlock = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Category\View\Info::class,
            '',
            ['dictionary' => $this->dictionary]
        );

        /** @var \M2E\Temu\Block\Adminhtml\Template\Category\View\Edit $editBlock */
        $editBlock = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Category\View\Edit::class,
            '',
            ['dictionary' => $this->dictionary]
        );

        $this->setChild('info', $infoBlock);
        $this->setChild('edit', $editBlock);

        return parent::_prepareLayout();
    }

    public function getInfoHtml()
    {
        return $this->getChildHtml('info');
    }

    public function getEditHtml()
    {
        return $this->getChildHtml('edit');
    }
}
