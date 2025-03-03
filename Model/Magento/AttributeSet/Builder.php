<?php

namespace M2E\Temu\Model\Magento\AttributeSet;

class Builder
{
    protected $productFactory;
    protected $entityAttributeSetFactory;

    /** @var \Magento\Eav\Model\Entity\Attribute\Set */
    protected $attributeSetObj = null;

    protected $setName = null;
    protected $params = [];

    protected $entityTypeId;
    protected $skeletonId;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $entityAttributeSetFactory
    ) {
        $this->entityAttributeSetFactory = $entityAttributeSetFactory;
        $this->productFactory = $productFactory;
    }

    public function save()
    {
        $this->init();

        return $this->saveAttributeSet();
    }

    // ---------------------------------------

    private function init()
    {
        if ($this->entityTypeId === null) {
            $this->entityTypeId = $this->productFactory->create()->getResource()->getTypeId();
        }

        if ($this->skeletonId !== null) {
            $skeletonAttributeSetId = $this->entityAttributeSetFactory->create()
                                                                      ->load($this->skeletonId)
                                                                      ->getId();

            !$skeletonAttributeSetId && $this->skeletonId = null;
        }
        !$this->skeletonId && $this->productFactory->create()->getDefaultAttributeSetId();

        $this->attributeSetObj = $this->entityAttributeSetFactory->create()
                                                                 ->load($this->setName, 'attribute_set_name');
    }

    private function saveAttributeSet()
    {
        if ($this->attributeSetObj->getId()) {
            return ['result' => true, 'obj' => $this->attributeSetObj];
        }

        $this->attributeSetObj->setEntityTypeId($this->entityTypeId)
                              ->setAttributeSetName($this->setName);

        try {
            $this->attributeSetObj->validate();
            $this->attributeSetObj->save();

            $this->attributeSetObj->initFromSkeleton($this->skeletonId)
                                  ->save();
        } catch (\Exception $e) {
            return ['result' => false, 'error' => $e->getMessage()];
        }

        return ['result' => true, 'obj' => $this->attributeSetObj];
    }

    //########################################

    public function setName($value)
    {
        $this->setName = $value;

        return $this;
    }

    public function setParams(array $value = [])
    {
        $this->params = $value;

        return $this;
    }

    public function setEntityTypeId($value)
    {
        $this->entityTypeId = $value;

        return $this;
    }

    public function setSkeletonAttributeSetId($value)
    {
        $this->skeletonId = $value;

        return $this;
    }
}
