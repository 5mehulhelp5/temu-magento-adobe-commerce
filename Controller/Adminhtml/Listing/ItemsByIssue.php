<?php

namespace M2E\Temu\Controller\Adminhtml\Listing;

class ItemsByIssue extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    /**
     * @ingeritdoc
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->setAjaxContent(
                $this->getLayout()->createBlock(
                    \M2E\Temu\Block\Adminhtml\Listing\ItemsByIssue\Grid::class
                )
            );

            return $this->getResult();
        }

        $this->addContent(
            $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Listing\ItemsByIssue::class)
        );
        $this->getResultPage()->getConfig()->getTitle()->prepend(__('Items By Issue'));

        return $this->getResult();
    }
}
