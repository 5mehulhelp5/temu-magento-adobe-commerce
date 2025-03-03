<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Manual\Schedule;

use M2E\Temu\Model\Product\Action\Manual\Schedule\AbstractSchedule;

class StopAndRemoveAction extends AbstractSchedule
{
    private \M2E\Temu\Model\Product\RemoveHandler $removeHandler;

    public function __construct(
        \M2E\Temu\Model\Product\RemoveHandler $removeHandler,
        \M2E\Temu\Model\ScheduledAction\CreateService $scheduledActionCreateService,
        \M2E\Temu\Model\Product\ActionCalculator $calculator,
        \M2E\Temu\Model\Listing\LogService $listingLogService
    ) {
        parent::__construct($scheduledActionCreateService, $calculator, $listingLogService);
        $this->removeHandler = $removeHandler;
    }

    protected function getAction(): int
    {
        return \M2E\Temu\Model\Product::ACTION_DELETE;
    }

    protected function prepareOrFilterProducts(array $listingsProducts): array
    {
        $result = [];
        foreach ($listingsProducts as $listingProduct) {
            if ($listingProduct->isStoppable()) {
                $result[] = $listingProduct;

                continue;
            }

            $this->removeHandler->process(
                $listingProduct,
                \M2E\Core\Helper\Data::INITIATOR_USER
            );
        }

        return $result;
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
    }
}
