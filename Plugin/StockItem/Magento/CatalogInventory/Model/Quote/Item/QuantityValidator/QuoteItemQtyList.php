<?php

namespace M2E\Temu\Plugin\StockItem\Magento\CatalogInventory\Model\Quote\Item\QuantityValidator;

use M2E\Temu\Model\Magento\Quote\Builder;

class QuoteItemQtyList extends \M2E\Temu\Plugin\AbstractPlugin
{
    private \M2E\Temu\Helper\Data\GlobalData $globalDataHelper;

    public function __construct(
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper
    ) {
        $this->globalDataHelper = $globalDataHelper;
    }

    public function aroundGetQty($interceptor, \Closure $callback, ...$arguments)
    {
        return $this->execute('getQty', $interceptor, $callback, $arguments);
    }

    /**
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList $interceptor
     * @param \Closure $callback
     * @param array $arguments
     *
     * @return mixed
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function processGetQty($interceptor, \Closure $callback, array $arguments)
    {
        $quoteItemId = $arguments[1];
        $quoteId = $arguments[2];
        $itemQty = &$arguments[3];

        if ($this->globalDataHelper->getValue(Builder::PROCESS_QUOTE_ID) == $quoteId) {
            empty($quoteItemId) && $itemQty = 0;
        }

        return $callback(...$arguments);
    }

    //########################################
}
