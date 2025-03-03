<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrl;

class Response
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
