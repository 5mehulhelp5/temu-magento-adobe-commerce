<?php

namespace M2E\Temu\Block\Adminhtml\Order\Item;

use M2E\Temu\Block\Adminhtml\Magento\AbstractContainer;

/**
 * Class \M2E\Temu\Block\Adminhtml\Order\Item\Edit
 */
class Edit extends AbstractContainer
{
    /** @var \M2E\Temu\Helper\Data */
    private $dataHelper;

    public function __construct(
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        \M2E\Temu\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    protected function _prepareLayout()
    {
        $this->jsUrl->addUrls([
            'order/assignProduct' => $this->getUrl('m2e_temu/order/assignProduct/'),
            'order/assignProductDetails' => $this->getUrl('m2e_temu/order/assignProductDetails/'),
            'order/assignToMagentoProduct' => $this->getUrl('m2e_temu/order/assignToMagentoProduct/'),
            'order/checkProductOptionStockAvailability' => $this->getUrl('m2e_temu/order/checkProductOptionStockAvailability/'),
            'order/createMagentoOrder' => $this->getUrl('m2e_temu/order/createMagentoOrder/'),
            'order/deleteNote' => $this->getUrl('m2e_temu/order/deleteNote/'),
            'order/getCountryRegions' => $this->getUrl('m2e_temu/order/getCountryRegions/'),
            'order/getDebugInformation' => $this->getUrl('m2e_temu/order/getDebugInformation/'),
            'order/getNotePopupHtml' => $this->getUrl('m2e_temu/order/getNotePopupHtml/'),
            'order/index' => $this->getUrl('m2e_temu/order/index/'),
            'order/reservationCancel' => $this->getUrl('m2e_temu/order/reservationCancel/'),
            'order/reservationPlace' => $this->getUrl('m2e_temu/order/reservationPlace/'),
            'order/resubmitShippingInfo' => $this->getUrl('m2e_temu/order/resubmitShippingInfo/'),
            'order/saveNote' => $this->getUrl('m2e_temu/order/saveNote/'),
            'order/shippingAddress' => $this->getUrl('m2e_temu/order/shippingAddress/'),
            'order/unAssignFromMagentoProduct' => $this->getUrl('m2e_temu/order/unAssignFromMagentoProduct/'),
            'order/view' => $this->getUrl('m2e_temu/order/view/'),
        ]);

        $this->jsUrl->addUrls([
            'log_order/grid' => $this->getUrl('m2e_temu/log_order/grid/'),
            'log_order/index' => $this->getUrl('m2e_temu/log_order/index/'),
        ]);

        $this->jsTranslator->addTranslations([
            'Please enter correct Product ID or SKU.' => __('Please enter correct Product ID or SKU.'),
            'Please enter correct Product ID.' => __('Please enter correct Product ID.'),
            'Edit Shipping Address' => __('Edit Shipping Address'),
        ]);

        $this->js->add(
            <<<JS
    require([
        'Temu/Order/Edit/Item'
    ], function(){
        window.OrderEditItemObj = new OrderEditItem();
    });
JS
        );

        return parent::_prepareLayout();
    }
}
