<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Manual\Schedule;

class StopAction extends AbstractSchedule
{
    use \M2E\Temu\Model\Product\Action\Manual\SkipMessageTrait;

    protected function getAction(): int
    {
        return \M2E\Temu\Model\Product::ACTION_STOP;
    }

    protected function calculateAction(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\ActionCalculator $calculator
    ): \M2E\Temu\Model\Product\Action {
        return \M2E\Temu\Model\Product\Action::createStop($product);
    }

    protected function logAboutSkipAction(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Listing\LogService $logService
    ): void {
        $logService->addProduct(
            $product,
            \M2E\Core\Helper\Data::INITIATOR_USER,
            \M2E\Temu\Model\Listing\Log::ACTION_STOP_PRODUCT,
            null,
            $this->createSkipStopMessage(),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_INFO,
        );
    }
}
