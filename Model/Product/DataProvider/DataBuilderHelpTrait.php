<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

trait DataBuilderHelpTrait
{
    /** @var string[] */
    private array $warningMessages = [];

    public function getWarningMessages(): array
    {
        return array_values($this->warningMessages);
    }

    protected function addWarningMessage(string $message): void
    {
        $this->warningMessages[sha1($message)] = $message;
    }

    protected function searchNotFoundAttributes(\M2E\Temu\Model\Magento\Product $magentoProduct): void
    {
        $magentoProduct->clearNotFoundAttributes();
    }
}
