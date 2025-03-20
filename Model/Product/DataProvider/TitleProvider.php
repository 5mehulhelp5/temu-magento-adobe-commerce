<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class TitleProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Title';

    private string $onlineTitle;

    /**
     * @param \M2E\Temu\Model\Product $product
     *
     * @return string
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function getTitle(\M2E\Temu\Model\Product $product): string
    {
        $title = $product->getDescriptionTemplateSource()->getTitle();

        if (strlen($title) > 70) {
            $title = substr($title, 0, 70);
        }

        $this->onlineTitle = $title;

        return $title;
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => ['online_title' => $this->onlineTitle],
        ];
    }
}
