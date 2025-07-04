<?php

namespace M2E\Temu\Model\Magento\Tax\Rule;

class Builder
{
    public const TAX_CLASS_NAME_PRODUCT = 'M2E Temu Connect Product Tax Class';
    public const TAX_CLASS_NAME_CUSTOMER = 'M2E Temu Connect Customer Tax Class';
    public const TAX_CLASS_NAME_SHIPPING = 'M2E Temu Connect Shipping Tax Class';

    public const TAX_RATE_CODE_PRODUCT = 'M2E Temu Connect Tax Rate';
    public const TAX_RULE_CODE_PRODUCT = 'M2E Temu Connect Tax Rule';

    public const TAX_RATE_CODE_SHIPPING = 'M2E Temu Connect Shipping Tax Rate';
    public const TAX_RULE_CODE_SHIPPING = 'M2E Temu Connect Shipping Tax Rule';

    private \Magento\Tax\Model\ClassModelFactory $classModelFactory;
    private \Magento\Tax\Model\Calculation\RateFactory $rateFactory;
    private \Magento\Tax\Model\Calculation\RuleFactory $ruleFactory;
    /** @var \Magento\Tax\Model\Calculation\Rule $rule */
    private $rule = null;

    public function __construct(
        \Magento\Tax\Model\ClassModelFactory $classModelFactory,
        \Magento\Tax\Model\Calculation\RateFactory $rateFactory,
        \Magento\Tax\Model\Calculation\RuleFactory $ruleFactory
    ) {
        $this->classModelFactory = $classModelFactory;
        $this->rateFactory = $rateFactory;
        $this->ruleFactory = $ruleFactory;
    }

    public function getRule()
    {
        return $this->rule;
    }

    public function buildProductTaxRule($rate, $countryId, $customerTaxClassId = null)
    {
        $this->buildTaxRule(
            $rate,
            $countryId,
            self::TAX_RATE_CODE_PRODUCT,
            self::TAX_RULE_CODE_PRODUCT,
            self::TAX_CLASS_NAME_PRODUCT,
            $customerTaxClassId
        );
    }

    public function buildShippingTaxRule($rate, $countryId, $customerTaxClassId = null)
    {
        $this->buildTaxRule(
            $rate,
            $countryId,
            self::TAX_RATE_CODE_SHIPPING,
            self::TAX_RULE_CODE_SHIPPING,
            self::TAX_CLASS_NAME_SHIPPING,
            $customerTaxClassId
        );
    }

    private function buildTaxRule(
        $rate,
        $countryId,
        $taxRateCode,
        $taxRuleCode,
        $taxClassName,
        $customerTaxClassId = null
    ) {
        // Init product tax class
        // ---------------------------------------
        $productTaxClass = $this->classModelFactory->create()->getCollection()
                                                   ->addFieldToFilter('class_name', $taxClassName)
                                                   ->addFieldToFilter(
                                                       'class_type',
                                                       \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
                                                   )
                                                   ->getFirstItem();

        if ($productTaxClass->getId() === null) {
            $productTaxClass->setClassName($taxClassName)
                            ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT);
            $productTaxClass->save();
        }
        // ---------------------------------------

        // Init customer tax class
        // ---------------------------------------
        if ($customerTaxClassId === null) {
            $customerTaxClass = $this->classModelFactory->create()->getCollection()
                                                        ->addFieldToFilter('class_name', self::TAX_CLASS_NAME_CUSTOMER)
                                                        ->addFieldToFilter(
                                                            'class_type',
                                                            \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER
                                                        )
                                                        ->getFirstItem();

            if ($customerTaxClass->getId() === null) {
                $customerTaxClass->setClassName(self::TAX_CLASS_NAME_CUSTOMER)
                                 ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER);
                $customerTaxClass->save();
            }

            $customerTaxClassId = $customerTaxClass->getId();
        }
        // ---------------------------------------

        // Init tax rate
        // ---------------------------------------
        $taxCalculationRate = $this->rateFactory->create()->load($taxRateCode, 'code');

        $taxCalculationRate->setCode($taxRateCode)
                           ->setRate((float)$rate)
                           ->setTaxCountryId((string)$countryId)
                           ->setTaxPostcode('*')
                           ->setTaxRegionId(0);
        $taxCalculationRate->save();
        // ---------------------------------------

        // Combine tax classes and tax rate in tax rule
        // ---------------------------------------
        $this->rule = $this->ruleFactory->create()->load($taxRuleCode, 'code');

        $this->rule->setCode($taxRuleCode)
                   ->setCustomerTaxClassIds([$customerTaxClassId])
                   ->setProductTaxClassIds([$productTaxClass->getId()])
                   ->setTaxRateIds([$taxCalculationRate->getId()])
                   ->setPriority(0);
        $this->rule->save();
        // ---------------------------------------
    }
}
