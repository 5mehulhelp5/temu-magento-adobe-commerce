<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Policy\SellingFormat;

class Source
{
    private ?\M2E\Temu\Model\Magento\Product $magentoProduct = null;
    private ?\M2E\Temu\Model\Policy\SellingFormat $sellingTemplateModel = null;

    public function setMagentoProduct(\M2E\Temu\Model\Magento\Product $magentoProduct): self
    {
        $this->magentoProduct = $magentoProduct;

        return $this;
    }

    public function getMagentoProduct(): ?\M2E\Temu\Model\Magento\Product
    {
        return $this->magentoProduct;
    }

    public function setSellingFormatTemplate(\M2E\Temu\Model\Policy\SellingFormat $instance): self
    {
        $this->sellingTemplateModel = $instance;

        return $this;
    }

    public function getSellingFormatTemplate(): ?\M2E\Temu\Model\Policy\SellingFormat
    {
        return $this->sellingTemplateModel;
    }
}
