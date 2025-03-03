<?php

namespace M2E\Temu\Model\Order;

class TagFactory
{
    /** @var \M2E\Temu\Model\TagFactory */
    private $tagFactory;

    public function __construct(\M2E\Temu\Model\TagFactory $tagFactory)
    {
        $this->tagFactory = $tagFactory;
    }

    public function createByErrorCode(string $errorCode, string $text): \M2E\Temu\Model\Tag
    {
        $text = $this->getPreparedText($errorCode) ?? $this->trimText($text);

        return $this->tagFactory->create($errorCode, $text);
    }

    private function getPreparedText(string $errorCode): ?string
    {
        return null;
    }

    private function trimText(string $text): string
    {
        if (strlen($text) <= 255) {
            return $text;
        }

        return substr($text, 0, 252) . '...';
    }
}
