<?php

namespace M2E\Temu\Model\Connector\Brands\Get;

class Processor
{
    private const PAGE_LIMIT = 5;

    private \M2E\Temu\Model\Connector\Client\Single $singleClient;

    public function __construct(
        \M2E\Temu\Model\Connector\Client\Single $singleClient
    ) {
        $this->singleClient = $singleClient;
    }

    public function processAuthorizedBrands(
        \M2E\Temu\Model\Account $account,
        string $region,
        string $categoryId = ''
    ) {
        return $this->process($account, $region, $categoryId, '', true);
    }

    private function process(
        \M2E\Temu\Model\Account $account,
        string $region,
        string $categoryId = '',
        string $brandName = '',
        ?bool $isAuthorized = null
    ): \M2E\Temu\Model\Connector\Brands\Get\Response {
        $accountHash = $account->getServerHash();

        $allBrands = [];
        $nextPageToken = null;
        $currentPage = 0;

        do {
            $currentPage++;

            $command = new \M2E\Temu\Model\Connector\Brands\GetCommand(
                $accountHash,
                $region,
                $categoryId,
                $brandName,
                $isAuthorized,
                $nextPageToken
            );

            /** @var \M2E\Temu\Model\Connector\Brands\Get\Response $response */
            $response = $this->singleClient->process($command);
            $allBrands = array_merge($allBrands, $response->getBrands());
            $nextPageToken = $response->getNextPageToken();
        } while ($nextPageToken !== null && self::PAGE_LIMIT > $currentPage);

        return new \M2E\Temu\Model\Connector\Brands\Get\Response(
            $allBrands,
            $response->getTotalCount(),
            null
        );
    }
}
