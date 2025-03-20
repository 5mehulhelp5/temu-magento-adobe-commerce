<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Connector\Category;

class GetCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private const RESPONSE_CATEGORIES_KEY = 'categories';

    private string $region;
    private ?int $parentId;

    public function __construct(string $region, int $parentId = null)
    {
        $this->region = $region;
        $this->parentId = $parentId;
    }

    public function getCommand(): array
    {
        return ['category', 'get', 'list'];
    }

    public function getRequestData(): array
    {
        return [
            'region' => $this->region,
            'parent_id' => $this->parentId
        ];
    }

    public function parseResponse(\M2E\Core\Model\Connector\Response $response): Get\Response
    {
        $this->processError($response);

        $result = new Get\Response();
        $responseData = $response->getResponseData();

        foreach ($responseData[self::RESPONSE_CATEGORIES_KEY] as $categoryData) {
            $result->addCategory(
                new Category(
                    $categoryData['id'],
                    $categoryData['title'],
                    $categoryData['is_leaf'],
                    $categoryData['parent_id']
                )
            );
        }

        return $result;
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
