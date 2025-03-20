<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Template\Category;

class Index extends \M2E\Temu\Controller\Adminhtml\Template\AbstractCategory
{
    public function execute()
    {
        $this->getResultPage()
             ->getConfig()
             ->getTitle()->prepend(__('Categories'));

        $content = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Category::class
        );
        $this->addContent($content);

        return $this->getResultPage();
    }
}
