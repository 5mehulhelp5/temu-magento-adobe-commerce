<?php

declare(strict_types=1);

namespace M2E\Temu\Helper\Module\Database;

class Tables
{
    public const PREFIX = 'm2e_temu_';

    public const TABLE_NAME_WIZARD = self::PREFIX . 'wizard';

    public const TABLE_NAME_ACCOUNT = self::PREFIX . 'account';
    public const TABLE_NAME_SHIPPING_PROVIDERS = self::PREFIX . 'shipping_providers';

    public const TABLE_NAME_LISTING = self::PREFIX . 'listing';
    public const TABLE_NAME_LISTING_LOG = self::PREFIX . 'listing_log';
    public const TABLE_NAME_LISTING_WIZARD = self::PREFIX . 'listing_wizard';
    public const TABLE_NAME_LISTING_WIZARD_STEP = self::PREFIX . 'listing_wizard_step';

    public const TABLE_NAME_LISTING_WIZARD_PRODUCT = self::PREFIX . 'listing_wizard_product';
    public const TABLE_NAME_PRODUCT = self::PREFIX . 'product';
    public const TABLE_NAME_PRODUCT_VARIANT_SKU = self::PREFIX . 'product_variant_sku';
    public const TABLE_NAME_PRODUCT_INSTRUCTION = self::PREFIX . 'product_instruction';
    public const TABLE_NAME_PRODUCT_SCHEDULED_ACTION = self::PREFIX . 'product_scheduled_action';
    public const TABLE_NAME_UNMANAGED_PRODUCT = self::PREFIX . 'unmanaged_product';
    public const TABLE_NAME_UNMANAGED_PRODUCT_VARIANT_SKU = self::PREFIX . 'unmanaged_product_variant_sku';

    public const TABLE_NAME_INVENTORY_SYNC_RECEIVED_PRODUCT = self::PREFIX . 'inventory_sync_received_product';
    public const TABLE_NAME_PRODUCT_LOCK = self::PREFIX . 'product_lock';

    public const TABLE_NAME_LOCK_ITEM = self::PREFIX . 'lock_item';
    public const TABLE_NAME_LOCK_TRANSACTIONAL = self::PREFIX . 'lock_transactional';
    public const TABLE_NAME_PROCESSING = self::PREFIX . 'processing';

    public const TABLE_NAME_PROCESSING_PARTIAL_DATA = self::PREFIX . 'processing_partial_data';

    public const TABLE_NAME_CATEGORY_TREE = self::PREFIX . 'category_tree';
    public const TABLE_NAME_CATEGORY_DICTIONARY = self::PREFIX . 'category_dictionary';
    public const TABLE_NAME_TEMPLATE_CATEGORY_ATTRIBUTES = self::PREFIX . 'template_category_attributes';

    public const TABLE_NAME_PROCESSING_LOCK = self::PREFIX . 'processing_lock';

    public const TABLE_NAME_STOP_QUEUE = self::PREFIX . 'stop_queue';
    public const TABLE_NAME_SYNCHRONIZATION_LOG = self::PREFIX . 'synchronization_log';
    public const TABLE_NAME_SYSTEM_LOG = self::PREFIX . 'system_log';

    public const TABLE_NAME_OPERATION_HISTORY = self::PREFIX . 'operation_history';
    public const TABLE_NAME_TEMPLATE_SELLING_FORMAT = self::PREFIX . 'template_selling_format';
    public const TABLE_NAME_TEMPLATE_SYNCHRONIZATION = self::PREFIX . 'template_synchronization';
    public const TABLE_NAME_TEMPLATE_DESCRIPTION = self::PREFIX . 'template_description';
    public const TABLE_NAME_TEMPLATE_SHIPPING = self::PREFIX . 'template_shipping';

    public const TABLE_NAME_TAG = self::PREFIX . 'tag';

    public const TABLE_NAME_PRODUCT_TAG_RELATION = self::PREFIX . 'product_tag_relation';

    public const TABLE_NAME_ORDER = self::PREFIX . 'order';
    public const TABLE_NAME_ORDER_ITEM = self::PREFIX . 'order_item';
    public const TABLE_NAME_ORDER_LOG = self::PREFIX . 'order_log';
    public const TABLE_NAME_ORDER_NOTE = self::PREFIX . 'order_note';

    public const TABLE_NAME_ORDER_CHANGE = self::PREFIX . 'order_change';

    /**
     * @return string[]
     */
    public static function getAllTables(): array
    {
        return array_keys(self::getTablesResourcesModels());
    }

    public static function getTableModel(string $tableName): string
    {
        $tablesModels = self::getTablesModels();

        return $tablesModels[$tableName];
    }

    public static function getTableResourceModel(string $tableName): string
    {
        $tablesModels = self::getTablesResourcesModels();

        return $tablesModels[$tableName];
    }

    private static function getTablesResourcesModels(): array
    {
        return [
            self::TABLE_NAME_ACCOUNT => \M2E\Temu\Model\ResourceModel\Account::class,
            self::TABLE_NAME_SHIPPING_PROVIDERS => \M2E\Temu\Model\ResourceModel\ShippingProvider::class,
            self::TABLE_NAME_LISTING => \M2E\Temu\Model\ResourceModel\Listing::class,
            self::TABLE_NAME_LISTING_LOG => \M2E\Temu\Model\ResourceModel\Listing\Log::class,
            self::TABLE_NAME_LISTING_WIZARD => \M2E\Temu\Model\ResourceModel\Listing\Wizard::class,
            self::TABLE_NAME_LISTING_WIZARD_STEP => \M2E\Temu\Model\ResourceModel\Listing\Wizard\Step::class,
            self::TABLE_NAME_LISTING_WIZARD_PRODUCT => \M2E\Temu\Model\ResourceModel\Listing\Wizard\Product::class,
            self::TABLE_NAME_PRODUCT => \M2E\Temu\Model\ResourceModel\Product::class,
            self::TABLE_NAME_PRODUCT_LOCK => \M2E\Temu\Model\ResourceModel\Product\Lock::class,
            self::TABLE_NAME_PRODUCT_INSTRUCTION => \M2E\Temu\Model\ResourceModel\Instruction::class,
            self::TABLE_NAME_PRODUCT_SCHEDULED_ACTION => \M2E\Temu\Model\ResourceModel\ScheduledAction::class,
            self::TABLE_NAME_LOCK_ITEM => \M2E\Temu\Model\ResourceModel\Lock\Item::class,
            self::TABLE_NAME_LOCK_TRANSACTIONAL => \M2E\Temu\Model\ResourceModel\Lock\Transactional::class,
            self::TABLE_NAME_PROCESSING => \M2E\Temu\Model\ResourceModel\Processing::class,
            self::TABLE_NAME_PROCESSING_LOCK => \M2E\Temu\Model\ResourceModel\Processing\Lock::class,
            self::TABLE_NAME_PROCESSING_PARTIAL_DATA => \M2E\Temu\Model\ResourceModel\Processing\PartialData::class,
            self::TABLE_NAME_STOP_QUEUE => \M2E\Temu\Model\ResourceModel\StopQueue::class,
            self::TABLE_NAME_SYNCHRONIZATION_LOG => \M2E\Temu\Model\ResourceModel\Synchronization\Log::class,
            self::TABLE_NAME_SYSTEM_LOG => \M2E\Temu\Model\ResourceModel\Log\System::class,
            self::TABLE_NAME_OPERATION_HISTORY => \M2E\Temu\Model\ResourceModel\OperationHistory::class,
            self::TABLE_NAME_TEMPLATE_SELLING_FORMAT => \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::class,
            self::TABLE_NAME_TEMPLATE_SYNCHRONIZATION => \M2E\Temu\Model\ResourceModel\Policy\Synchronization::class,
            self::TABLE_NAME_TEMPLATE_DESCRIPTION => \M2E\Temu\Model\ResourceModel\Policy\Description::class,
            self::TABLE_NAME_TEMPLATE_SHIPPING => \M2E\Temu\Model\ResourceModel\Policy\Shipping::class,
            self::TABLE_NAME_WIZARD => \M2E\Temu\Model\ResourceModel\Wizard::class,
            self::TABLE_NAME_TAG => \M2E\Temu\Model\ResourceModel\Tag::class,
            self::TABLE_NAME_PRODUCT_TAG_RELATION => \M2E\Temu\Model\ResourceModel\Tag\ListingProduct\Relation::class,
            self::TABLE_NAME_ORDER => \M2E\Temu\Model\ResourceModel\Order::class,
            self::TABLE_NAME_ORDER_ITEM => \M2E\Temu\Model\ResourceModel\Order\Item::class,
            self::TABLE_NAME_ORDER_LOG => \M2E\Temu\Model\ResourceModel\Order\Log::class,
            self::TABLE_NAME_ORDER_NOTE => \M2E\Temu\Model\ResourceModel\Order\Note::class,
            self::TABLE_NAME_ORDER_CHANGE => \M2E\Temu\Model\ResourceModel\Order\Change::class,
            self::TABLE_NAME_UNMANAGED_PRODUCT => \M2E\Temu\Model\ResourceModel\UnmanagedProduct::class,
            self::TABLE_NAME_INVENTORY_SYNC_RECEIVED_PRODUCT => \M2E\Temu\Model\ResourceModel\InventorySync\ReceivedProduct::class,
            self::TABLE_NAME_PRODUCT_VARIANT_SKU => \M2E\Temu\Model\ResourceModel\Product\VariantSku::class,
            self::TABLE_NAME_UNMANAGED_PRODUCT_VARIANT_SKU => \M2E\Temu\Model\ResourceModel\UnmanagedProduct\VariantSku::class,
            self::TABLE_NAME_CATEGORY_TREE => \M2E\Temu\Model\ResourceModel\Category\Tree::class,
            self::TABLE_NAME_CATEGORY_DICTIONARY => \M2E\Temu\Model\ResourceModel\Category\Dictionary::class,
            self::TABLE_NAME_TEMPLATE_CATEGORY_ATTRIBUTES => \M2E\Temu\Model\ResourceModel\Category\Attribute::class,
        ];
    }

    private static function getTablesModels(): array
    {
        return [
            self::TABLE_NAME_ACCOUNT => \M2E\Temu\Model\Account::class,
            self::TABLE_NAME_SHIPPING_PROVIDERS => \M2E\Temu\Model\ShippingProvider::class,
            self::TABLE_NAME_LISTING => \M2E\Temu\Model\Listing::class,
            self::TABLE_NAME_LISTING_LOG => \M2E\Temu\Model\Listing\Log::class,
            self::TABLE_NAME_LISTING_WIZARD => \M2E\Temu\Model\Listing\Wizard::class,
            self::TABLE_NAME_LISTING_WIZARD_STEP => \M2E\Temu\Model\Listing\Wizard\Step::class,
            self::TABLE_NAME_LISTING_WIZARD_PRODUCT => \M2E\Temu\Model\Listing\Wizard\Product::class,
            self::TABLE_NAME_PRODUCT => \M2E\Temu\Model\Product::class,
            self::TABLE_NAME_PRODUCT_LOCK => \M2E\Temu\Model\Product\Lock::class,
            self::TABLE_NAME_PRODUCT_INSTRUCTION => \M2E\Temu\Model\Instruction::class,
            self::TABLE_NAME_PRODUCT_SCHEDULED_ACTION => \M2E\Temu\Model\ScheduledAction::class,
            self::TABLE_NAME_LOCK_ITEM => \M2E\Temu\Model\Lock\Item::class,
            self::TABLE_NAME_LOCK_TRANSACTIONAL => \M2E\Temu\Model\Lock\Transactional::class,
            self::TABLE_NAME_PROCESSING => \M2E\Temu\Model\Processing::class,
            self::TABLE_NAME_PROCESSING_LOCK => \M2E\Temu\Model\Processing\Lock::class,
            self::TABLE_NAME_PROCESSING_PARTIAL_DATA => \M2E\Temu\Model\Processing\PartialData::class,
            self::TABLE_NAME_STOP_QUEUE => \M2E\Temu\Model\StopQueue::class,
            self::TABLE_NAME_SYNCHRONIZATION_LOG => \M2E\Temu\Model\Synchronization\Log::class,
            self::TABLE_NAME_SYSTEM_LOG => \M2E\Temu\Model\Log\System::class,
            self::TABLE_NAME_OPERATION_HISTORY => \M2E\Temu\Model\OperationHistory::class,
            self::TABLE_NAME_TEMPLATE_SELLING_FORMAT => \M2E\Temu\Model\Policy\SellingFormat::class,
            self::TABLE_NAME_TEMPLATE_SYNCHRONIZATION => \M2E\Temu\Model\Policy\Synchronization::class,
            self::TABLE_NAME_TEMPLATE_DESCRIPTION => \M2E\Temu\Model\Policy\Description::class,
            self::TABLE_NAME_TEMPLATE_SHIPPING => \M2E\Temu\Model\Policy\Shipping::class,
            self::TABLE_NAME_WIZARD => \M2E\Temu\Model\Wizard::class,
            self::TABLE_NAME_TAG => \M2E\Temu\Model\Tag\Entity::class,
            self::TABLE_NAME_PRODUCT_TAG_RELATION => \M2E\Temu\Model\Tag\ListingProduct\Relation::class,
            self::TABLE_NAME_ORDER => \M2E\Temu\Model\Order::class,
            self::TABLE_NAME_ORDER_ITEM => \M2E\Temu\Model\Order\Item::class,
            self::TABLE_NAME_ORDER_LOG => \M2E\Temu\Model\Order\Log::class,
            self::TABLE_NAME_ORDER_NOTE => \M2E\Temu\Model\Order\Note::class,
            self::TABLE_NAME_ORDER_CHANGE => \M2E\Temu\Model\Order\Change::class,
            self::TABLE_NAME_UNMANAGED_PRODUCT => \M2E\Temu\Model\UnmanagedProduct::class,
            self::TABLE_NAME_INVENTORY_SYNC_RECEIVED_PRODUCT => \M2E\Temu\Model\InventorySync\ReceivedProduct::class,
            self::TABLE_NAME_PRODUCT_VARIANT_SKU => \M2E\Temu\Model\Product\VariantSku::class,
            self::TABLE_NAME_UNMANAGED_PRODUCT_VARIANT_SKU => \M2E\Temu\Model\UnmanagedProduct\VariantSku::class,
            self::TABLE_NAME_CATEGORY_TREE => \M2E\Temu\Model\Category\Tree::class,
            self::TABLE_NAME_CATEGORY_DICTIONARY => \M2E\Temu\Model\Category\Dictionary::class,
            self::TABLE_NAME_TEMPLATE_CATEGORY_ATTRIBUTES => \M2E\Temu\Model\Category\CategoryAttribute::class,
        ];
    }

    // ----------------------------------------

    public static function isModuleTable(string $tableName): bool
    {
        return strpos($tableName, self::PREFIX) !== false;
    }
}
