<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Product\Grid;

class Issues extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    public function execute()
    {
        $this->getResultPage()
             ->getConfig()
             ->getTitle()
             ->prepend(__('Items By Issue'));

        return $this->getResult();
    }
}
