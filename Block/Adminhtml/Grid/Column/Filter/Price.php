<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Grid\Column\Filter;

class Price extends \M2E\Temu\Block\Adminhtml\Magento\Grid\Column\Filter\Range
{
    public function getValue($index = null)
    {
        if ($index) {
            return $this->getData('value', $index);
        }
        $value = $this->getData('value');
        if (
            (isset($value['from']) && $value['from'] !== '')
            || (isset($value['to']) && $value['to'] !== '')
            || (isset($value['on_promotion']) && $value['on_promotion'] !== '')
        ) {
            return $value;
        }

        return null;
    }
}
