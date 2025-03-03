<?php

namespace M2E\Temu\Observer\Product\Attribute\Update;

class Before extends \M2E\Temu\Observer\AbstractObserver
{
    private \M2E\Temu\PublicServices\Product\SqlChange $sqlChange;

    public function __construct(
        \M2E\Temu\PublicServices\Product\SqlChange $sqlChange
    ) {
        $this->sqlChange = $sqlChange;
    }

    protected function process(): void
    {
        $changedProductsIds = $this->getEventObserver()->getData('product_ids');
        if (empty($changedProductsIds)) {
            return;
        }

        foreach ($changedProductsIds as $productId) {
            $this->sqlChange->markProductChanged($productId);
        }

        $this->sqlChange->applyChanges();
    }
}
