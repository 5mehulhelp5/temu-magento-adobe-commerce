<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Account\Edit\Form;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Render extends \Magento\Backend\Block\Template implements RendererInterface
{
    private \M2E\Core\Helper\Magento\Carriers $magentoCarriersHelper;
    private \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository;

    public function __construct(
        \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository,
        \M2E\Core\Helper\Magento\Carriers $magentoCarriersHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\Json\Helper\Data $jsonHelper = null,
        ?\Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->magentoCarriersHelper = $magentoCarriersHelper;
        $this->shippingProviderRepository = $shippingProviderRepository;
    }

    protected $_template = 'M2E_Temu::template/shipping_provider/account_mapping.phtml';
    private \M2E\Temu\Block\Adminhtml\Account\Edit\Form\Element\ShippingProviderMapping $element;

    /**
     * @param \M2E\Temu\Block\Adminhtml\Account\Edit\Form\Element\ShippingProviderMapping $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;

        return $this->toHtml();
    }

    public function getAccount(): \M2E\Temu\Model\Account
    {
        return $this->element->getAccount();
    }

    /**
     * @return array{code: string, name: string, required: bool}
     */
    public function getMagentoCarriers(): array
    {
        $default = [
            'code' => 'default',
            'name' => __('Default'),
            'required' => true,
            'tooltip' => __(
                'Unless a specific %channel_title Carrier is explicitly assigned for a Magento Carrier, ' .
                'the %channel_title Carrier value selected for the \'Default\' Magento carrier will be used.',
                [
                    'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                ],
            ),
        ];

        return array_merge(
            [$default],
            array_map(function ($carrier) {
                return [
                    'name' => $carrier->getConfigData('title'),
                    'code' => $carrier->getCarrierCode(),
                    'required' => false,
                    'tooltip' => '',
                ];
            }, $this->magentoCarriersHelper->getCarriersWithAvailableTracking())
        );
    }

    /**
     * @return array{label: string, value: string}
     */
    public function getShippingProviders(): array
    {
        $shippingProviders = $this
            ->shippingProviderRepository
            ->getByAccount($this->getAccount());

        if (count($shippingProviders) === 0) {
            return [];
        }

        $result = [
            'default' => [
                'label' => __('None'),
                'value' => '',
            ],
        ];

        foreach ($shippingProviders as $item) {
            $result[$item->getShippingProviderId()] = [
                'label' => $item->getShippingProviderName(),
                'value' => $item->getShippingProviderId(),
            ];
        }

        return array_values($result);
    }

    public function getRegions(): array
    {
        $shippingProviders = $this
            ->shippingProviderRepository
            ->getByAccount($this->getAccount());

        if (count($shippingProviders) === 0) {
            return [];
        }

        $regions = [];

        foreach ($shippingProviders as $item) {
            $regions[$item->getShippingProviderRegionId()] = [
                'label' => $item->getShippingProviderRegionName(),
                'value' => $item->getShippingProviderRegionId(),
            ];
        }

        return $regions;
    }

    public function renderTableRow(
        int $index,
        array $region,
        array $magentoCarrier
    ): string {
        $channelOptions = $this->getShippingProviders();

        if (count($channelOptions) === 0) {
            return '';
        }

        $tableColumns = [
            sprintf('<td>%s</td>', $region['label']),
        ];

        $tableColumns[] = sprintf(
            '<td><span class="%s">%s</span>%s</td>',
            $magentoCarrier['required'] ? 'required-field' : '',
            $magentoCarrier['name'],
            $this->renderTooltip((string)$magentoCarrier['tooltip'])
        );

        $selectHtml = sprintf(
            '<select name="%s" class="admin__control-select" style="width:100%%" %s>',
            $this->makeOptionName($magentoCarrier['code'], $region['value']),
            $magentoCarrier['required'] ? 'required' : '',
        );
        foreach ($channelOptions as $option) {
            $isSelected = $this->isSelected(
                $region['value'],
                $magentoCarrier['code'],
                (int)$option['value']
            );

            $selectHtml .= sprintf(
                '<option value="%s"%s>%s</option>',
                $option['value'],
                $isSelected ? ' selected' : '',
                $option['label']
            );
        }
        $selectHtml .= '</select>';

        $tableColumns[] = sprintf('<td>%s</td>', $selectHtml);

        return sprintf(
            '<tr class="%s %s">%s</tr>',
            $index % 2 ? '_odd-row' : '',
            $index === 0 ? 'new-shop-row' : '',
            implode('', $tableColumns)
        );
    }

    private function makeOptionName(string $carrierCode, int $regionId): string
    {
        return sprintf(
            'shipping_provider_mapping[%s][%s]',
            $regionId,
            $carrierCode
        );
    }

    private function isSelected(
        int $regionId,
        string $carrierCode,
        int $shippingProviderId
    ): bool {
        $existMappings = $this->element->getExistShippingProviderMapping();

        if (!isset($existMappings[$regionId][$carrierCode])) {
            return false;
        }

        return (int)$existMappings[$regionId][$carrierCode] === $shippingProviderId;
    }

    private function renderTooltip(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return <<<HTML
<div class="Temu-field-tooltip Temu-field-tooltip-right Temu-fieldset-tooltip admin__field-tooltip">
    <a class="admin__field-tooltip-action" href="javascript://"></a>
    <div class="admin__field-tooltip-content">$text</div>
</div>
HTML;
    }
}
