<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Manual\Realtime;

class StopAndRemoveAction extends AbstractRealtime
{
    private \M2E\Temu\Model\Product\RemoveHandler $removeHandler;

    public function __construct(
        \M2E\Temu\Model\Product\RemoveHandler $removeHandler,
        \M2E\Temu\Model\Product\Action\Dispatcher $actionDispatcher,
        \M2E\Temu\Model\Product\ActionCalculator $calculator,
        \M2E\Temu\Model\Listing\LogService $listingLogService
    ) {
        parent::__construct($actionDispatcher, $calculator, $listingLogService);
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
