<?php

namespace M2E\Temu\Block\Adminhtml\Listing\Wizard\Category\Add\Summary;

class Grid extends \M2E\Temu\Block\Adminhtml\Category\Grid
{
    /** @var \M2E\Temu\Helper\Module\Database\Structure */
    private $databaseHelper;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Magento\Category\CollectionFactory $categoryCollectionFactory,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \M2E\Temu\Helper\Module\Database\Structure $databaseHelper,
        array $data = []
    ) {
        $this->databaseHelper = $databaseHelper;
        parent::__construct(
            $categoryCollectionFactory,
            $context,
            $backendHelper,
            $data,
        );
    }

    public function setProductsForEachCategory($productsForEachCategory)
    {
        $this->setData('products_for_each_category', $productsForEachCategory);

        return $this;
    }

    public function getProductsForEachCategory()
    {
        return $this->getData('products_for_each_category');
    }

    public function setProductsIds($productsIds)
    {
        $this->setData('products_ids', $productsIds);

        return $this;
    }

    public function getProductsIds()
    {
        return $this->getData('products_ids');
    }

    // ---------------------------------------

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('listingProductSourceCategoriesSummaryGrid');
        // ---------------------------------------

        // Set default values
        // ---------------------------------------
        $this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        // ---------------------------------------
    }

    // ---------------------------------------

    protected function _prepareCollection()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name');

        $dbSelect = $collection->getConnection()
                               ->select()
                               ->from(
                                   $this->databaseHelper->getTableNameWithPrefix('catalog_category_product'),
                                   'category_id',
                               )
                               ->where('`product_id` IN(?)', $this->getProductsIds());

        $collection->getSelect()->where('entity_id IN (' . $dbSelect->__toString() . ')');

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    // ---------------------------------------

    protected function _prepareMassaction()
    {
        // Set massaction identifiers
        // ---------------------------------------
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        // ---------------------------------------

        $this->getMassactionBlock()->addItem('remove', [
            'label' => __('Remove'),
        ]);

        // ---------------------------------------

        return parent::_prepareMassaction();
    }

    // ---------------------------------------

    protected function _prepareColumns()
    {
        $this->addColumn('magento_category', [
            'header' => __('Magento Category'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'name',
            'filter' => false,
            'sortable' => false,
            'frame_callback' => [$this, 'callbackColumnMagentoCategory'],
        ]);

        $this->addColumn('action', [
            'header' => __('Action'),
            'align' => 'center',
            'width' => '75px',
            'type' => 'text',
            'filter' => false,
            'sortable' => false,
            'frame_callback' => [$this, 'callbackColumnActions'],
        ]);

        return parent::_prepareColumns();
    }

    // ---------------------------------------

    public function callbackColumnMagentoCategory($value, $row, $column, $isExport)
    {
        $productsForEachCategory = $this->getProductsForEachCategory();

        return parent::callbackColumnMagentoCategory($value, $row, $column, $isExport) .
            ' (' . $productsForEachCategory[$row->getId()] . ')';
    }

    // ---------------------------------------

    public function callbackColumnActions($value, $row, $column, $isExport)
    {
        $removeText = __('Remove');

        return <<<HTML
<a  href="javascript:"
    onclick="ListingWizardProductSummaryGridObj.selectByRowId('{$row->getId()}');
             ListingWizardProductSummaryGridObj.remove()"
   >$removeText</a>
HTML;
    }

    // ---------------------------------------

    protected function _toHtml()
    {
        $beforeHtml = <<<HTML
<style>

    div#{$this->getId()} div.grid {
        overflow-y: auto !important;
        height: 263px !important;
    }

    div#{$this->getId()} div.grid th {
        padding: 2px 4px !important;
    }

    div#{$this->getId()} div.grid td {
        padding: 2px 4px !important;
    }

    div#{$this->getId()} table.massaction div.right {
        display: block;
    }

    div#{$this->getId()} table.massaction td {
        padding: 1px 8px;
    }

</style>
HTML;

        $help = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\HelpBlock::class)->setData([
            'content' => __('The Quantity of chosen Products in each Category is shown in brackets.<br/>' .
                'If the Product belongs to several Categories, it is shown in each Category. ' .
                'And if you remove the Category with such Product it will be subtracted from each Category.'),
        ]);

        $beforeHtml .= <<<HTML
<div style="margin: 15px 0 10px 0">{$help->toHtml()}</div>
HTML;

        $path = 'listing_wizard_product/removeProductsByCategory';
        $this->jsUrl->add($this->getUrl('*/' . $path), $path);

        if (!$this->getRequest()->getParam('grid')) {
            $this->js->add(
                <<<JS
    require([
        'Temu/Listing/Wizard/Product/Summary/Grid'
    ],function() {
        ListingWizardProductSummaryGridObj = new ListingWizardProductSummaryGrid(
            '{$this->getId()}'
        );
    });
JS,
            );
        }

        $this->js->add(
            <<<JS
    require([
        'Temu/Listing/Wizard/Product/Summary/Grid'
    ],function() {
        {$this->getCollection()->getSize()} || closeCategoriesPopup();
        ListingWizardProductSummaryGridObj.afterInitPage();
    });
JS,
        );

        if ($this->getRequest()->getParam('grid')) {
            $beforeHtml = null;
        }

        return $beforeHtml . parent::_toHtml();
    }

    // ---------------------------------------

    public function getGridUrl()
    {
        return $this->getCurrentUrl(['grid' => true]);
    }

    // ---------------------------------------

    public function getRowUrl($item)
    {
        return false;
    }
}
