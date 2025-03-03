<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Order\Get;

use M2E\Temu\Model\Channel\Connector\Order\Get\Items\Response;

class ItemsByCreateDateCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    use CommandTrait;

    private \DateTimeInterface $updateFrom;
    private \DateTimeInterface $updateTo;
    private string $accountHash;

    public function __construct(
        string $accountHash,
        \DateTimeInterface $updateFrom,
        \DateTimeInterface $updateTo
    ) {
        $this->accountHash = $accountHash;
        $this->updateFrom = $updateFrom;
        $this->updateTo = $updateTo;
    }

    public function getRequestData(): array
    {
        return [
            'account' => $this->accountHash,
            'from_create_date' => $this->updateFrom->format('Y-m-d H:i:s'),
            'to_create_date' => $this->updateTo->format('Y-m-d H:i:s'),
        ];
    }
}
