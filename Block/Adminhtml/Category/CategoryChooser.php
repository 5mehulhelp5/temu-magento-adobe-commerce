<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Category;

class CategoryChooser extends \M2E\Temu\Block\Adminhtml\Magento\AbstractBlock
{
    protected $_template = 'category/category_chooser.phtml';

    private ?string $selectedCategory;

    public function __construct(
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        ?string $selectedCategory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->selectedCategory = $selectedCategory;
    }

    public function getSelectedCategory(): ?string
    {
        return $this->selectedCategory;
    }
}
