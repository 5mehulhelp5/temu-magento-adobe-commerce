<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel;

class Account
{
    public string $identifier;
    public int $siteId;
    public string $siteTitle;

    public function __construct(
        string $identifier,
        int $siteId,
        string $siteTitle
    ) {
        $this->identifier = $identifier;
        $this->siteId = $siteId;
        $this->siteTitle = $siteTitle;
    }
}
