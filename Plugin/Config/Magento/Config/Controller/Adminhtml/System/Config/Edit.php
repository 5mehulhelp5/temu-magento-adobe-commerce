<?php

namespace M2E\Temu\Plugin\Config\Magento\Config\Controller\Adminhtml\System\Config;

class Edit extends \M2E\Temu\Plugin\AbstractPlugin
{
    private \M2E\Temu\Helper\Module\Maintenance $moduleManitenanceHelper;

    public function __construct(
        \M2E\Temu\Helper\Module\Maintenance $moduleManitenanceHelper
    ) {
        $this->moduleManitenanceHelper = $moduleManitenanceHelper;
    }

    protected function canExecute(): bool
    {
        if ($this->moduleManitenanceHelper->isEnabled()) {
            return false;
        }

        return true;
    }

    public function aroundExecute($interceptor, \Closure $callback, ...$arguments)
    {
        return $this->execute('execute', $interceptor, $callback, $arguments);
    }

    // ---------------------------------------

    protected function processExecute($interceptor, \Closure $callback, array $arguments)
    {
        $result = $callback(...$arguments);

        if ($result instanceof \Magento\Backend\Model\View\Result\Redirect) {
            return $result;
        }

        $result->getConfig()->addPageAsset('M2E_Temu::css/help_block.css');
        $result->getConfig()->addPageAsset('M2E_Temu::css/system/config.css');

        return $result;
    }
}
