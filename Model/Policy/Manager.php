<?php

namespace M2E\Temu\Model\Policy;

class Manager
{
    private $ownerObject = null;
    private $templateNick = null;

    public const MODE_PARENT = 0;
    public const MODE_CUSTOM = 1;
    public const MODE_TEMPLATE = 2;

    public const COLUMN_PREFIX = 'template';

    public const TEMPLATE_DESCRIPTION = 'description';
    public const TEMPLATE_SELLING_FORMAT = 'selling_format';
    public const TEMPLATE_SYNCHRONIZATION = 'synchronization';
    public const TEMPLATE_SHIPPING = 'shipping';

    protected \M2E\Temu\Model\ActiveRecord\Factory $activeRecordFactory;

    private \M2E\Temu\Model\Policy\SellingFormatFactory $sellingFormatFactory;
    private \M2E\Temu\Model\Policy\SynchronizationFactory $synchronizationFactory;
    private \M2E\Temu\Model\Policy\DescriptionFactory $descriptionFactory;
    private \M2E\Temu\Model\Policy\ShippingFactory $shippingFactory;

    public function __construct(
        \M2E\Temu\Model\Policy\SellingFormatFactory $sellingFormatFactory,
        \M2E\Temu\Model\Policy\SynchronizationFactory $synchronizationFactory,
        \M2E\Temu\Model\Policy\DescriptionFactory $descriptionFactory,
        \M2E\Temu\Model\Policy\ShippingFactory $shippingFactory,
        \M2E\Temu\Model\ActiveRecord\Factory $activeRecordFactory
    ) {
        $this->activeRecordFactory = $activeRecordFactory;

        $this->sellingFormatFactory = $sellingFormatFactory;
        $this->synchronizationFactory = $synchronizationFactory;
        $this->descriptionFactory = $descriptionFactory;
        $this->shippingFactory = $shippingFactory;
    }

    //########################################

    /**
     * @return \M2E\Temu\Model\Listing|\M2E\Temu\Model\Product|null
     */
    public function getOwnerObject()
    {
        return $this->ownerObject;
    }

    /**
     * @param \M2E\Temu\Model\Listing|\M2E\Temu\Model\Product $object
     *
     * @return $this
     * @throws \M2E\Temu\Model\Exception
     */
    public function setOwnerObject($object): self
    {
        if (
            !($object instanceof \M2E\Temu\Model\Listing) &&
            !($object instanceof \M2E\Temu\Model\Product)
        ) {
            throw new \M2E\Temu\Model\Exception('Owner object is out of knowledge range.');
        }

        $this->ownerObject = $object;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTemplateNick(): ?string
    {
        return $this->templateNick;
    }

    /**
     * @throws \M2E\Temu\Model\Exception
     */
    public function setTemplate(string $nick): self
    {
        if (!in_array(strtolower($nick), $this->getAllTemplates())) {
            throw new \M2E\Temu\Model\Exception('Policy nick is out of knowledge range.');
        }

        $this->templateNick = strtolower($nick);

        return $this;
    }

    //########################################

    /**
     * @return array
     */
    public function getAllTemplates(): array
    {
        return [
            self::TEMPLATE_SELLING_FORMAT,
            self::TEMPLATE_SYNCHRONIZATION,
            self::TEMPLATE_DESCRIPTION,
            self::TEMPLATE_SHIPPING,
        ];
    }

    //########################################

    public function getModeColumnName(): string
    {
        return self::COLUMN_PREFIX . '_' . $this->getTemplateNick() . '_mode';
    }

    public function getTemplateIdColumnName(): string
    {
        return self::COLUMN_PREFIX . '_' . $this->getTemplateNick() . '_id';
    }

    //########################################

    public function getIdColumnValue()
    {
        if ($this->isModeParent()) {
            return null;
        }

        return $this->getOwnerObject()->getData($this->getTemplateIdColumnName());
    }

    //########################################

    public function getModeValue()
    {
        return $this->getOwnerObject()->getData($this->getModeColumnName());
    }

    /**
     * @return bool
     */
    public function isModeParent()
    {
        return $this->getModeValue() == self::MODE_PARENT;
    }

    /**
     * @deprecated
     * @return null|string
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function getTemplateModelName(): ?string
    {
        $name = null;

        switch ($this->getTemplateNick()) {
            case self::TEMPLATE_SELLING_FORMAT:
                $name = 'Temu_Template_SellingFormat';
                break;
            case self::TEMPLATE_SYNCHRONIZATION:
                $name = 'Temu_Template_Synchronization';
                break;
            case self::TEMPLATE_DESCRIPTION:
                $name = 'Temu_Template_Description';
                break;
            case self::TEMPLATE_SHIPPING:
                $name = 'Temu_Template_Shipping';
                break;
        }

        if ($name === null) {
            throw new \M2E\Temu\Model\Exception\Logic(
                sprintf('Template nick "%s" is unknown.', $this->getTemplateNick())
            );
        }

        return $name;
    }

    public function getTemplateModel()
    {
        switch ($this->getTemplateNick()) {
            case self::TEMPLATE_SELLING_FORMAT:
                return $this->sellingFormatFactory->create();
            case self::TEMPLATE_SYNCHRONIZATION:
                return $this->synchronizationFactory->create();
            case self::TEMPLATE_DESCRIPTION:
                return $this->descriptionFactory->create();
            case self::TEMPLATE_SHIPPING:
                return $this->shippingFactory->createEmpty();
        }

        throw new \M2E\Temu\Model\Exception\Logic(
            sprintf('Template nick "%s" is unknown.', $this->getTemplateNick())
        );
    }
}
