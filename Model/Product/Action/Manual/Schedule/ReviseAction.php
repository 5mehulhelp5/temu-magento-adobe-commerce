<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Manual\Schedule;

class ReviseAction extends AbstractSchedule
{
    use \M2E\Temu\Model\Product\Action\Manual\SkipMessageTrait;

    protected function getAction(): int
    {
        return \M2E\Temu\Model\Product::ACTION_REVISE;
    }

    protected function calculateAction(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\ActionCalculator $calculator
    ): \M2E\Temu\Model\Product\Action {
        $result = $calculator->calculateToReviseOrStop($product);
        if ($result->isActionStop()) {
            return \M2E\Temu\Model\Product\Action::createNothing($product);
        }

        return $result;
    }

    protected function logAboutSkipAction(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Listing\LogService $logService
    ): void {
        $logService->addProduct(
            $product,
            \M2E\Core\Helper\Data::INITIATOR_USER,
            \M2E\Temu\Model\Listing\Log::ACTION_REVISE_PRODUCT,
            null,
            $this->createSkipReviseMessage(),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_INFO,
        );
    }
}
