<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

use M2E\Temu\Model\ResourceModel\Listing as ListingResource;

class Listing extends \M2E\Temu\Model\ActiveRecord\AbstractModel
{
    public const LOCK_NICK = 'listing';

    public const INSTRUCTION_TYPE_PRODUCT_ADDED = 'listing_product_added';
    public const INSTRUCTION_INITIATOR_ADDING_PRODUCT = 'adding_product_to_listing';

    public const INSTRUCTION_TYPE_PRODUCT_MOVED_FROM_OTHER = 'listing_product_moved_from_other';
    public const INSTRUCTION_INITIATOR_MOVING_PRODUCT_FROM_OTHER = 'moving_product_from_other_to_listing';

    public const INSTRUCTION_TYPE_PRODUCT_MOVED_FROM_LISTING = 'listing_product_moved_from_listing';
    public const INSTRUCTION_INITIATOR_MOVING_PRODUCT_FROM_LISTING = 'moving_product_from_listing_to_listing';

    public const INSTRUCTION_TYPE_PRODUCT_REMAP_FROM_LISTING = 'listing_product_remap_from_listing';

    public const INSTRUCTION_TYPE_CHANGE_LISTING_STORE_VIEW = 'change_listing_store_view';
    public const INSTRUCTION_INITIATOR_CHANGED_LISTING_STORE_VIEW = 'changed_listing_store_view';

    public const CREATE_LISTING_SESSION_DATA = 'temu_listing_create';

    private \M2E\Temu\Model\Account $account;
    private \M2E\Temu\Model\Policy\SellingFormat $templateSellingFormat;
    private \M2E\Temu\Model\Policy\Synchronization $templateSynchronization;
    private \M2E\Temu\Model\Product\Repository $listingProductRepository;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Policy\SellingFormat\Repository $sellingFormatTemplateRepository;
    private \M2E\Temu\Model\Policy\Synchronization\Repository $synchronizationTemplateRepository;

    public function __construct(
        \M2E\Temu\Model\Product\Repository $listingProductRepository,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Policy\SellingFormat\Repository $sellingFormatTemplateRepository,
        \M2E\Temu\Model\Policy\Synchronization\Repository $synchronizationTemplateRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data,
        );
        $this->listingProductRepository = $listingProductRepository;
        $this->accountRepository = $accountRepository;
        $this->sellingFormatTemplateRepository = $sellingFormatTemplateRepository;
        $this->synchronizationTemplateRepository = $synchronizationTemplateRepository;
    }

    // ----------------------------------------

    public function _construct(): void
    {
        parent::_construct();
        $this->_init(\M2E\Temu\Model\ResourceModel\Listing::class);
    }

    // ----------------------------------------

    public function getAccount(): \M2E\Temu\Model\Account
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->account)) {
            return $this->account;
        }

        return $this->account = $this->accountRepository->get($this->getAccountId());
    }

    // ----------------------------------------

    /**
     * @return \M2E\Temu\Model\Product[]
     */
    public function getProducts(): array
    {
        $products = $this->listingProductRepository->findByListing($this);
        foreach ($products as $product) {
            $product->initListing($this);
        }

        return $products;
    }

    // ----------------------------------------

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function getTemplateSellingFormat(): Policy\SellingFormat
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->templateSellingFormat)) {
            $this->templateSellingFormat = $this->sellingFormatTemplateRepository
                ->get($this->getTemplateSellingFormatId());
        }

        return $this->templateSellingFormat;
    }

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function getTemplateSynchronization(): Policy\Synchronization
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->templateSynchronization)) {
            $this->templateSynchronization = $this->synchronizationTemplateRepository
                ->get($this->getTemplateSynchronizationId());
        }

        return $this->templateSynchronization;
    }

    public function getTitle(): string
    {
        return (string)$this->getData(ListingResource::COLUMN_TITLE);
    }

    public function getAccountId(): int
    {
        return (int)$this->getData(ListingResource::COLUMN_ACCOUNT_ID);
    }

    public function getStoreId(): int
    {
        return (int)$this->getData(ListingResource::COLUMN_STORE_ID);
    }

    public function getCreateDate()
    {
        return $this->getData(ListingResource::COLUMN_CREATE_DATE);
    }

    public function getUpdateDate()
    {
        return $this->getData(ListingResource::COLUMN_UPDATE_DATE);
    }

    public function setTemplateSellingFormatId(int $sellingFormatTemplateId): void
    {
        $this->setData(ListingResource::COLUMN_TEMPLATE_SELLING_FORMAT_ID, $sellingFormatTemplateId);
    }

    public function getTemplateSellingFormatId(): int
    {
        return (int)$this->getData(ListingResource::COLUMN_TEMPLATE_SELLING_FORMAT_ID);
    }

    public function setTemplateDescriptionId(int $descriptionTemplateId): void
    {
        $this->setData(ListingResource::COLUMN_TEMPLATE_DESCRIPTION_ID, $descriptionTemplateId);
    }

    public function getTemplateDescriptionId(): int
    {
        return (int)$this->getData(ListingResource::COLUMN_TEMPLATE_DESCRIPTION_ID);
    }

    public function setTemplateSynchronizationId(int $synchronizationTemplateId): void
    {
        $this->setData(ListingResource::COLUMN_TEMPLATE_SYNCHRONIZATION_ID, $synchronizationTemplateId);
    }

    public function getTemplateSynchronizationId(): int
    {
        return (int)$this->getData(ListingResource::COLUMN_TEMPLATE_SYNCHRONIZATION_ID);
    }

    // ---------------------------------------

    public function getAdditionalData(): array
    {
        $data = $this->getData(ListingResource::COLUMN_ADDITIONAL_DATA);
        if ($data === null) {
            return [];
        }

        return json_decode($data, true);
    }

    public function setAdditionalData(array $additionalData): void
    {
        $this->setData(
            ListingResource::COLUMN_ADDITIONAL_DATA,
            json_encode($additionalData, JSON_THROW_ON_ERROR),
        );
    }

    public function setStoreId(int $id): void
    {
        $this->setData(ListingResource::COLUMN_STORE_ID, $id);
    }
}
