<?php

namespace M2E\Temu\Model\Connector\Brands;

class GetCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $accountHash;
    private string $shopId;
    private string $categoryId;
    private ?bool $isAuthorized;
    private string $brandName;
    private ?string $nextPageToken;

    public function __construct(
        string $accountHash,
        string $shopId,
        string $categoryId = '',
        string $brandName = '',
        ?bool $isAuthorized = null,
        ?string $nextPageToken = null
    ) {
        $this->accountHash = $accountHash;
        $this->shopId = $shopId;
        $this->categoryId = $categoryId;
        $this->isAuthorized = $isAuthorized;
        $this->brandName = $brandName;
        $this->nextPageToken = $nextPageToken;
    }

    public function getCommand(): array
    {
        return ['category', 'get', 'brands'];
    }

    public function getRequestData(): array
    {
        $requestParams = [
            'account' => $this->accountHash,
            'shop_id' => $this->shopId,
        ];

        if ($this->categoryId !== '') {
            $requestParams['category_id'] = $this->categoryId;
        }

        if ($this->brandName !== '') {
            $requestParams['brand_name'] = $this->brandName;
        }

        if ($this->isAuthorized !== null) {
            $requestParams['is_authorized'] = $this->isAuthorized;
        }

        if ($this->nextPageToken !== null) {
            $requestParams['page_token'] = $this->nextPageToken;
        }

        return $requestParams;
    }

    public function parseResponse(\M2E\Core\Model\Connector\Response $response): object
    {
        $this->processError($response);

        $responseData = $response->getResponseData();

        return new \M2E\Temu\Model\Connector\Brands\Get\Response(
            $responseData['list'],
            $responseData['total'],
            $responseData['next_page_token'] ?? null
        );
    }

    private function processError(\M2E\Core\Model\Connector\Response $response): void
    {
        if (!$response->isResultError()) {
            return;
        }

        foreach ($response->getMessageCollection()->getMessages() as $message) {
            if ($message->isError()) {
                throw new \M2E\Temu\Model\Exception\CategoryInvalid(
                    $message->getText(),
                    [],
                    (int)$message->getCode()
                );
            }
        }
    }
}
