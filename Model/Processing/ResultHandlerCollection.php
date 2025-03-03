<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Processing;

class ResultHandlerCollection
{
    private const MAP = [
        \M2E\Temu\Model\InventorySync\Processing\ResultHandler::NICK =>
            \M2E\Temu\Model\InventorySync\Processing\ResultHandler::class,
        \M2E\Temu\Model\Product\Action\Async\Processing\ResultHandler::NICK =>
            \M2E\Temu\Model\Product\Action\Async\Processing\ResultHandler::class,
    ];

    public function has(string $nick): bool
    {
        return isset(self::MAP[$nick]);
    }

    /**
     * @param string $nick
     *
     * @return string result handler class name
     */
    public function get(string $nick): string
    {
        return self::MAP[$nick];
    }
}
