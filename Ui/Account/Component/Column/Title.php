<?php

declare(strict_types=1);

namespace M2E\Temu\Ui\Account\Component\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Title extends Column
{
    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$row) {
            $accountTitle = $row['title'];
            $site = $row['site_title'];

            $html = \M2E\Core\Helper\Data::escapeHtml($accountTitle);
            $html .= $this->renderLine((string)\__('Site'), $site);

            $row['title'] = $html;
        }

        return $dataSource;
    }

    private function renderLine(string $label, string $value): string
    {
        return sprintf('<p style="margin: 0">%s: %s</p>', $label, $value);
    }
}
