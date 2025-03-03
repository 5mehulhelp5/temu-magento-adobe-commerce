<?php

namespace M2E\Temu\Controller\Adminhtml\Policy;

use M2E\Temu\Controller\Adminhtml\AbstractTemplate;

class NewTemplateHtml extends AbstractTemplate
{
    public function execute()
    {
        $nick = $this->getRequest()->getParam('nick');

        $this->setAjaxContent(
            $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Template\NewTemplate\Form::class
            )
                 ->setData('nick', $nick)
        );

        return $this->getResult();
    }
}
