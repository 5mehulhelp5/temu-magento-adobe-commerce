<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Manual\Realtime;

class RelistAction extends AbstractRealtime
{
    use \M2E\Temu\Model\Product\Action\Manual\SkipMessageTrait;

    protected function getAction(): int
    {
        return \M2E\Temu\Model\Product::ACTION_RELIST;
    }

    protected function calculateAction(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\ActionCalculator $calculator
    ): \M2E\Temu\Model\Product\Action {
        return $calculator->calculateToRelist($product, \M2E\Temu\Model\Product::STATUS_CHANGER_USER);
    }

    protected function logAboutSkipAction(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Listing\LogService $logService
    ): void {
        $logService->addProduct(
            $product,
            \M2E\Core\Helper\Data::INITIATOR_USER,
            \M2E\Temu\Model\Listing\Log::ACTION_RELIST_PRODUCT,
            null,
            $this->createSkipRelistMessage(),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_INFO,
        );
    }
}
