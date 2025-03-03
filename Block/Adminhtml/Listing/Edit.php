<?php

namespace M2E\Temu\Block\Adminhtml\Listing;

class Edit extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractContainer
{
    private ?\M2E\Temu\Model\Listing $listing = null;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;
    private \M2E\Core\Helper\Url $urlHelper;

    public function __construct(
        \M2E\Core\Helper\Url $urlHelper,
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        $this->listingRepository = $listingRepository;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        $this->setId('temuListingEdit');
        $this->_controller = 'adminhtml_listing';
        $this->_mode = 'create_templates';

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');

        if ($this->getRequest()->getParam('back')) {
            $url = $this->urlHelper->getBackUrl();
            $this->addButton(
                'back',
                [
                    'label' => __('Back'),
                    'onclick' => 'TemuListingSettingsObj.backClick(\'' . $url . '\')',
                    'class' => 'back',
                ]
            );
        }

        $backUrl = $this->urlHelper->getBackUrlParam('list');

        $url = $this->getUrl(
            '*/listing/save',
            [
                'id' => $this->getListing()->getId(),
                'back' => $backUrl,
            ]
        );
        $saveButtonsProps = [
            'save' => [
                'label' => __('Save And Back'),
                'onclick' => 'TemuListingSettingsObj.saveClick(\'' . $url . '\')',
                'class' => 'save primary',
            ],
        ];

        $editBackUrl = $this->urlHelper->makeBackUrlParam(
            $this->getUrl(
                '*/listing/edit',
                [
                    'id' => $this->listing['id'],
                    'back' => $backUrl,
                ]
            )
        );
        $url = $this->getUrl(
            '*/listing/save',
            [
                'id' => $this->listing['id'],
                'back' => $editBackUrl,
            ]
        );
        $saveButtons = [
            'id' => 'save_and_continue',
            'label' => __('Save And Continue Edit'),
            'class' => 'add',
            'button_class' => '',
            'onclick' => 'TemuListingSettingsObj.saveAndEditClick(\'' . $url . '\', 1)',
            'class_name' => \M2E\Temu\Block\Adminhtml\Magento\Button\SplitButton::class,
            'options' => $saveButtonsProps,
        ];

        $this->addButton('save_buttons', $saveButtons);
    }

    protected function getListing(): ?\M2E\Temu\Model\Listing
    {
        if ($this->listing === null && $this->getRequest()->getParam('id')) {
            $this->listing = $this->listingRepository->get($this->getRequest()->getParam('id'));
        }

        return $this->listing;
    }
}
