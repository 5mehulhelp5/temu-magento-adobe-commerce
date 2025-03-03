<?php

declare(strict_types=1);

namespace M2E\Temu\Model\HealthStatus\Task\Server\Status;

use M2E\Temu\Model\HealthStatus\Task\IssueType;
use M2E\Temu\Model\HealthStatus\Task\Result as TaskResult;

class SystemLogs extends IssueType
{
    private const COUNT_CRITICAL_LEVEL = 1500;
    private const COUNT_WARNING_LEVEL = 500;
    private const SEE_TO_BACK_INTERVAL = 3600;

    private TaskResult\Factory $resultFactory;
    private \Magento\Framework\UrlInterface $urlBuilder;
    private \M2E\Temu\Model\Log\System\Repository $systemLogRepository;

    public function __construct(
        \M2E\Temu\Model\Log\System\Repository $systemLogRepository,
        \M2E\Temu\Model\HealthStatus\Task\Result\Factory $resultFactory,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->resultFactory = $resultFactory;
        $this->urlBuilder = $urlBuilder;
        $this->systemLogRepository = $systemLogRepository;
    }

    public function process(): TaskResult
    {
        $exceptionsCount = $this->getExceptionsCountByOneHourBackInterval();

        $result = $this->resultFactory->create($this);
        $result->setTaskResult(TaskResult::STATE_SUCCESS);
        $result->setTaskData($exceptionsCount);

        if ($exceptionsCount >= self::COUNT_WARNING_LEVEL) {
            $result->setTaskResult(TaskResult::STATE_WARNING);
            $result->setTaskMessage(
                __(
                    '%extension_title has recorded <b>%exception_count</b> messages to the System Log during the ' .
                    'last hour. <a target="_blank" href="%url">Click here</a> for the details.',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                        'exception_count' => $exceptionsCount,
                        'url' => $this->urlBuilder->getUrl('m2e_temu/synchronization_log/index')
                    ]
                )
            );
        }

        if ($exceptionsCount >= self::COUNT_CRITICAL_LEVEL) {
            $result->setTaskResult(TaskResult::STATE_CRITICAL);
            $result->setTaskMessage(
                __(
                    '%extension_title has recorded <b>%exception_count</b> messages to the System Log ' .
                    'during the last hour. <a href="%url">Click here</a> for the details.',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                        'exception_count' => $exceptionsCount,
                        'url' => $this->urlBuilder->getUrl('m2e_temu/synchronization_log/index')
                    ]
                )
            );
        }

        return $result;
    }

    private function getExceptionsCountByOneHourBackInterval(): int
    {
        $date = \M2E\Core\Helper\Date::createCurrentGmt();
        $date->modify('- ' . self::SEE_TO_BACK_INTERVAL . ' seconds');

        return $this->systemLogRepository->findExceptionsCountByBackInterval($date);
    }
}
