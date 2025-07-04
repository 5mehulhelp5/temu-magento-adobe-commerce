<?php

namespace M2E\Temu\Observer;

abstract class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var null|\Magento\Framework\Event\Observer
     */
    private $eventObserver = null;

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if (!$this->isAllowedProcess()) {
            return;
        }

        try {
            $this->setEventObserver($observer);

            if (!$this->canProcess()) {
                return;
            }

            $this->beforeProcess();

            $this->process();

            $this->afterProcess();
        } catch (\Throwable $exception) {
            $this->getObjectManager()->get(\M2E\Temu\Helper\Module\Exception::class)->process($exception);
        }
    }

    protected function canProcess(): bool
    {
        return true;
    }

    abstract protected function process(): void;

    protected function beforeProcess(): void
    {
    }

    protected function afterProcess(): void
    {
    }

    // ----------------------------------------

    private function setEventObserver(\Magento\Framework\Event\Observer $eventObserver): void
    {
        $this->eventObserver = $eventObserver;
    }

    protected function getEventObserver(): \Magento\Framework\Event\Observer
    {
        if (!($this->eventObserver instanceof \Magento\Framework\Event\Observer)) {
            throw new \M2E\Temu\Model\Exception\Logic('Property "eventObserver" should be set first.');
        }

        return $this->eventObserver;
    }

    protected function getEvent(): \Magento\Framework\Event
    {
        return $this->getEventObserver()->getEvent();
    }

    // ----------------------------------------

    private function isAllowedProcess(): bool
    {
        $moduleHelper = $this->getObjectManager()->get(\M2E\Temu\Helper\Module::class);

        return $this->getObjectManager()->get(\M2E\Core\Helper\Magento::class)->isInstalled()
            && !$this->getObjectManager()->get(\M2E\Temu\Helper\Module\Maintenance::class)->isEnabled()
            && !$moduleHelper->isDisabled()
            && $moduleHelper->isReadyToWork();
    }

    private function getObjectManager(): \Magento\Framework\App\ObjectManager
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }
}
