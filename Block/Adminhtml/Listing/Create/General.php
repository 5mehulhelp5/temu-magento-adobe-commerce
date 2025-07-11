<?php

namespace M2E\Temu\Block\Adminhtml\Listing\Create;

class General extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractContainer
{
    public function _construct()
    {
        parent::_construct();

        $this->setId('listingCreateGeneral');
        $this->_controller = 'adminhtml_listing_create';
        $this->_mode = 'general';

        $this->_headerText = __('Creating A New Listing');

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');

        $this->addButton(
            'next',
            [
                'label' => __('Next Step'),
                'class' => 'action-primary next_step_button forward',
            ]
        );
    }

    protected function _toHtml()
    {
        $breadcrumb = $this->getLayout()
                           ->createBlock(\M2E\Temu\Block\Adminhtml\Listing\Create\Breadcrumb::class);
        $breadcrumb->setSelectedStep(1);

        $helpBlock = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\HelpBlock::class);
        $helpBlock->addData(
            [
                'content' => __(
                    '<p>It is necessary to select an %channel_title Account ' .
                    '(existing or create a new one) as well as choose a Shop that you are going to sell ' .
                    'Magento Products on.</p><br><p>It is also important to specify a Store View in accordance with ' .
                    'which Magento Attribute values will be used in the Listing settings.</p>',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
                'style' => 'margin-top: 30px',
            ]
        );

        return
            $breadcrumb->_toHtml() .
            '<div id="progress_bar"></div>' .
            $helpBlock->toHtml() .
            '<div id="content_container">' . parent::_toHtml() . '</div>';
    }
}
