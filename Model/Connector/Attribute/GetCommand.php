<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Connector\Attribute;

class GetCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $region;
    private int $categoryId;

    public function __construct(string $region, int $categoryId)
    {
        $this->region = $region;
        $this->categoryId = $categoryId;
    }

    public function getCommand(): array
    {
        return ['category', 'get', 'attributes'];
    }

    public function getRequestData(): array
    {
        return [
            'region' => $this->region,
            'category_id' => $this->categoryId,
        ];
    }

    public function parseResponse(\M2E\Core\Model\Connector\Response $response): Get\Response
    {
        $this->processError($response);
        $responseData = $response->getResponseData();

        $attributes = [];
        foreach ($responseData['attributes'] as $attributeData) {
            $rules = [
                'min_value' => $attributeData['rules']['min_value'],
                'max_value' => $attributeData['rules']['min_value'],
            ];
            $attribute = new Attribute(
                (string)$attributeData['id'],
                $attributeData['name'],
                $this->getAttributeType($attributeData['is_sale']),
                $attributeData['is_sale'],
                $attributeData['is_required'],
                $attributeData['is_customized'],
                $attributeData['is_multiple_selected'],
                $attributeData['type'],
                $rules,
                $attributeData['pid'],
                $attributeData['ref_pid'],
                $attributeData['template_pid'],
                $attributeData['parent_spec_id']
            );

            foreach ($attributeData['options'] as $value) {
                $attribute->addValue(
                    (string)$value['id'],
                    $value['value'],
                    $value['spec_id'],
                    $value['group_id']
                );
            }

            $attributes[] = $attribute;
        }

        return new \M2E\Temu\Model\Connector\Attribute\Get\Response(
            $attributes,
            [
                /*'size_chart' => [
                    'is_supported' => $responseData['rules']['size_chart']['is_supported'],
                    'is_required' => $responseData['rules']['size_chart']['is_required'],
                ]*/
            ]
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

    private function getAttributeType(bool $isSale): string
    {
        return $isSale ? Attribute::SALES_TYPE : Attribute::PRODUCT_TYPE;
    }
}
