<?php

declare(strict_types=1);

namespace M2E\Temu\Helper\Component;

class Temu
{
    public const MAX_LENGTH_FOR_OPTION_VALUE = 50;

    private const MARKETPLACE_CURRENCY_MAPPING = [
        105 => 'EUR',
        100 => 'USD',
        102 => 'GBP',
        159 => 'ALL',
        175 => 'DZD',
        157 => 'EUR',
        189 => 'ARS',
        166 => 'AMD',
        103 => 'AUD',
        143 => 'EUR',
        167 => 'AZN',
        134 => 'BHD',
        142 => 'EUR',
        158 => 'BAM',
        132 => 'BRL',
        188 => 'BND',
        141 => 'BGN',
        101 => 'CAD',
        125 => 'CLP',
        164 => 'COP',
        146 => 'EUR',
        117 => 'EUR',
        137 => 'CZK',
        139 => 'DKK',
        172 => 'DOP',
        178 => 'USD',
        183 => 'USD',
        149 => 'EUR',
        144 => 'EUR',
        106 => 'EUR',
        165 => 'GEL',
        115 => 'EUR',
        138 => 'HUF',
        156 => 'ISK',
        116 => 'EUR',
        135 => 'ILS',
        107 => 'EUR',
        118 => 'JPY',
        131 => 'JOD',
        162 => 'KZT',
        123 => 'KWD',
        150 => 'EUR',
        148 => 'EUR',
        152 => 'EUR',
        160 => 'MKD',
        126 => 'MYR',
        151 => 'EUR',
        170 => 'MUR',
        110 => 'MXN',
        154 => 'MDL',
        155 => 'EUR',
        171 => 'MAD',
        108 => 'EUR',
        104 => 'NZD',
        124 => 'NOK',
        133 => 'OMR',
        176 => 'USD',
        163 => 'PEN',
        127 => 'PHP',
        112 => 'PLN',
        111 => 'EUR',
        130 => 'QAR',
        140 => 'RON',
        120 => 'SAR',
        153 => 'RSD',
        121 => 'SGD',
        145 => 'EUR',
        147 => 'EUR',
        136 => 'ZAR',
        119 => 'KRW',
        109 => 'EUR',
        113 => 'SEK',
        114 => 'CHF',
        129 => 'THB',
        179 => 'TTD',
        174 => 'TRY',
        168 => 'UAH',
        122 => 'AED',
        169 => 'UYU',
        181 => 'UZS',
        187 => 'VND',

    ];

    /**
     * @param array $options
     *
     * @return array
     */
    public static function prepareOptionsForOrders(array $options): array
    {
        foreach ($options as &$singleOption) {
            if ($singleOption instanceof \Magento\Catalog\Model\Product) {
                $reducedName = trim(
                    \M2E\Core\Helper\Data::reduceWordsInString(
                        $singleOption->getName(),
                        self::MAX_LENGTH_FOR_OPTION_VALUE
                    )
                );
                $singleOption->setData('name', $reducedName);

                continue;
            }

            foreach ($singleOption['values'] as &$singleOptionValue) {
                foreach ($singleOptionValue['labels'] as &$singleOptionLabel) {
                    $singleOptionLabel = trim(
                        \M2E\Core\Helper\Data::reduceWordsInString(
                            $singleOptionLabel,
                            self::MAX_LENGTH_FOR_OPTION_VALUE
                        )
                    );
                }
            }
        }

        if (isset($options['additional']['attributes'])) {
            foreach ($options['additional']['attributes'] as $code => &$title) {
                $title = trim($title);
            }
            unset($title);
        }

        return $options;
    }

    public static function getCurrencyCodeBySiteId(int $siteId): string
    {
        if (!array_key_exists($siteId, self::MARKETPLACE_CURRENCY_MAPPING)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Currency not defined for the given site.')
            );
        }

        return self::MARKETPLACE_CURRENCY_MAPPING[$siteId];
    }
}
