<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider;

use M2E\Temu\Model\Product\DataProvider\DataBuilderHelpTrait;
use M2E\Temu\Model\Product\DataProvider\DataBuilderInterface;

class ReferenceLinkProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'ReferenceLink';

    public function getReferenceLink(\M2E\Temu\Model\Product\VariantSku $variantSku): ?string
    {
        $referenceLinkAttributeCode = $variantSku->getSellingFormatTemplate()->getReferenceLinkAttribute();
        if ($referenceLinkAttributeCode === null) {
            return null;
        }

        $magentoProduct = $variantSku->getMagentoProduct();

        return $magentoProduct->getAttributeValue($referenceLinkAttributeCode);
    }
}
