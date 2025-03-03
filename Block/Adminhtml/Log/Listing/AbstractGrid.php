<?php

namespace M2E\Temu\Block\Adminhtml\Log\Listing;

abstract class AbstractGrid extends \M2E\Temu\Block\Adminhtml\Log\AbstractGrid
{
    /** @var \M2E\Temu\Model\ResourceModel\Collection\WrapperFactory */
    protected $wrapperCollectionFactory;
    /** @var \M2E\Temu\Model\ResourceModel\Collection\CustomFactory */
    protected $customCollectionFactory;
    /** @var \M2E\Temu\Model\Config\Manager */
    private $config;
    /** @var \M2E\Temu\Helper\Data */
    protected $dataHelper;

    public function __construct(
        \M2E\Temu\Model\Config\Manager $config,
        \M2E\Temu\Model\ResourceModel\Collection\WrapperFactory $wrapperCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Collection\CustomFactory $customCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \M2E\Temu\Helper\View $viewHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \M2E\Temu\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->config = $config;
        $this->wrapperCollectionFactory = $wrapperCollectionFactory;
        $this->customCollectionFactory = $customCollectionFactory;
        $this->dataHelper = $dataHelper;

        parent::__construct($resourceConnection, $viewHelper, $context, $backendHelper, $data);
    }

    abstract protected function getViewMode();

    abstract protected function getLogHash($type);

    protected function addMaxAllowedLogsCountExceededNotification($date)
    {
        $notification = \M2E\Core\Helper\Data::escapeJs(
            (string)__(
                'Using a Grouped View Mode, the logs records which are not older than %date are ' .
                'displayed here in order to prevent any possible Performance-related issues.',
                ['date' => $this->_localeDate->formatDate($date, \IntlDateFormatter::MEDIUM, true)],
            )
        );

        $this->js->add("Temu.formData.maxAllowedLogsCountExceededNotification = '{$notification}';");
    }

    protected function getMaxLastHandledRecordsCount()
    {
        return $this->config->get(
            '/logs/grouped/',
            'max_records_count'
        );
    }

    public function getRowUrl($item)
    {
        return false;
    }
}
