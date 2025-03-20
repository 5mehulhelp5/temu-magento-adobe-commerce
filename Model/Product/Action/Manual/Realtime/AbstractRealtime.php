<?php

namespace M2E\Temu\Model\Product\Action\Manual\Realtime;

use M2E\Temu\Model\Product\Action\Manual\Result;

abstract class AbstractRealtime extends \M2E\Temu\Model\Product\Action\Manual\AbstractManual
{
    private \M2E\Temu\Model\Product\Action\Dispatcher $actionDispatcher;

    public function __construct(
        \M2E\Temu\Model\Product\Action\Dispatcher $actionDispatcher,
        \M2E\Temu\Model\Product\ActionCalculator $calculator,
        \M2E\Temu\Model\Listing\LogService $listingLogService
    ) {
        parent::__construct($calculator, $listingLogService);
        $this->actionDispatcher = $actionDispatcher;
    }

    protected function processAction(array $actions, array $params): Result
    {
        $params['logs_action_id'] = $this->getLogActionId();
        /**
         * @var \M2E\Temu\Model\Product\Action $action
         */
        foreach ($actions as $action) {
            switch ($this->getAction()) {
                case \M2E\Temu\Model\Product::ACTION_LIST:
                    $result = $this->actionDispatcher->processList(
                        $action->getProduct(),
                        $action->getConfigurator(),
                        $action->getVariantSettings(),
                        $params,
                        \M2E\Temu\Model\Product::STATUS_CHANGER_USER
                    );
                    break;
                case \M2E\Temu\Model\Product::ACTION_REVISE:
                    $result = $this->actionDispatcher->processRevise(
                        $action->getProduct(),
                        $action->getConfigurator(),
                        $action->getVariantSettings(),
                        $params,
                        \M2E\Temu\Model\Product::STATUS_CHANGER_USER
                    );
                    break;
                case \M2E\Temu\Model\Product::ACTION_STOP:
                    $result = $this->actionDispatcher->processStop(
                        $action->getProduct(),
                        $action->getConfigurator(),
                        $action->getVariantSettings(),
                        $params,
                        \M2E\Temu\Model\Product::STATUS_CHANGER_USER
                    );
                    break;
                case \M2E\Temu\Model\Product::ACTION_DELETE:
                    $result = $this->actionDispatcher->processDelete(
                        $action->getProduct(),
                        $action->getConfigurator(),
                        $action->getVariantSettings(),
                        $params,
                        \M2E\Temu\Model\Product::STATUS_CHANGER_USER
                    );
                    break;
                case \M2E\Temu\Model\Product::ACTION_RELIST:
                    $result = $this->actionDispatcher->processRelist(
                        $action->getProduct(),
                        $action->getConfigurator(),
                        $action->getVariantSettings(),
                        $params,
                        \M2E\Temu\Model\Product::STATUS_CHANGER_USER
                    );
                    break;

                default:
                    throw new \DomainException("Unknown action '{$this->getAction()}'");
            }
        }

        if ($result === \M2E\Core\Helper\Data::STATUS_ERROR) {
            return Result::createError($this->getLogActionId());
        }

        if ($result === \M2E\Core\Helper\Data::STATUS_WARNING) {
            return Result::createWarning($this->getLogActionId());
        }

        return Result::createSuccess($this->getLogActionId());
    }
}
