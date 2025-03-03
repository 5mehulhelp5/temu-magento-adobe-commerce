<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

trait DataProviderTrait
{
    /** @var \M2E\Temu\Model\Product\DataProvider\DataBuilderInterface[] */
    private array $dataBuilders = [];

    /** @var \M2E\Temu\Model\Product\DataProvider\AbstractResult[] */
    private array $results = [];

    /**
     * @return string[]
     */
    public function getLogs(): array
    {
        $result = [];
        foreach ($this->dataBuilders as $dataBuilder) {
            $message = $dataBuilder->getWarningMessages();
            if (empty($message)) {
                continue;
            }

            array_push($result, ...$message);
        }

        return $result;
    }

    // ----------------------------------------

    private function getBuilder(
        string $nick
    ): \M2E\Temu\Model\Product\DataProvider\DataBuilderInterface {
        if (isset($this->dataBuilders[$nick])) {
            return $this->dataBuilders[$nick];
        }

        return $this->dataBuilders[$nick] = $this->dataBuilderFactory->create($nick);
    }

    private function addResult(string $builderNick, DataProvider\AbstractResult $result): void
    {
        $this->results[$builderNick] = $result;
    }

    private function hasResult(string $builderNick): bool
    {
        return isset($this->results[$builderNick]);
    }

    private function getResult(string $builderNick): \M2E\Temu\Model\Product\DataProvider\AbstractResult
    {
        return $this->results[$builderNick];
    }
}
