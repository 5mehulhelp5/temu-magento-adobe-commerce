<?php

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Module;

use M2E\Temu\Controller\Adminhtml\Context;
use M2E\Temu\Controller\Adminhtml\ControlPanel\AbstractCommand;

class Integration extends AbstractCommand
{
    private \Magento\Framework\Data\Form\FormKey $formKey;
    private \M2E\Temu\Model\Product\Action\Type\Revise\RequestFactory $reviseRequestFactory;
    private \M2E\Temu\Model\Product\Action\Type\Relist\RequestFactory $relistRequestFactory;
    private \M2E\Temu\Model\Product\Repository $productRepository;
    private \M2E\Temu\Model\Product\ActionCalculator $actionCalculator;
    private \M2E\Temu\Model\Product\Action\Type\Stop\RequestFactory $stopRequestFactory;
    private \M2E\Temu\Model\Product\Action\LogBufferFactory $logBufferFactory;

    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \M2E\Temu\Helper\View\ControlPanel $controlPanelHelper,
        \M2E\Temu\Model\Product\Action\Type\Revise\RequestFactory $reviseRequestFactory,
        \M2E\Temu\Model\Product\Action\Type\Relist\RequestFactory $relistRequestFactory,
        \M2E\Temu\Model\Product\Action\Type\Stop\RequestFactory $stopRequestFactory,
        \M2E\Temu\Model\Product\Repository $productRepository,
        \M2E\Temu\Model\Product\ActionCalculator $actionCalculator,
        \M2E\Temu\Model\Product\Action\LogBufferFactory $logBufferFactory,
        Context $context
    ) {
        parent::__construct($controlPanelHelper, $context);
        $this->formKey = $formKey;
        $this->reviseRequestFactory = $reviseRequestFactory;
        $this->relistRequestFactory = $relistRequestFactory;
        $this->productRepository = $productRepository;
        $this->actionCalculator = $actionCalculator;
        $this->stopRequestFactory = $stopRequestFactory;
        $this->logBufferFactory = $logBufferFactory;
    }

    /**
     * @title "Print Request Data"
     * @description "Calculate Allowed Action for Listing Product"
     */
    public function getRequestDataAction()
    {
        $httpRequest = $this->getRequest();

        $listingProductMagentoSku = $httpRequest->getParam('listing_product_magento_sku', null);

        $form = $this->printFormForCalculateAction($listingProductMagentoSku);
        $html = "<div style='padding: 20px;background:#d3d3d3;position:sticky;top:0;width:100vw'>$form</div>";

        if ($httpRequest->getParam('print')) {
            try {
                $listingProducts = $this->productRepository->findProductsByMagentoSku($listingProductMagentoSku);
                foreach ($listingProducts as $listingProduct) {
                    $action = $this->actionCalculator->calculate(
                        $listingProduct,
                        true,
                        \M2E\Temu\Model\Product::STATUS_CHANGER_USER,
                    );

                    $html .= '<div style="margin: 20px 0">' . $this->printProductInfo($listingProduct, $action) . '</div>';
                }
            } catch (\Throwable $exception) {
                $html .= sprintf(
                    '<div style="margin: 20px 0">%s</div>',
                    $exception->getMessage()
                );
            }
        }

        return $html;
    }

    private function printFormForCalculateAction(?string $listingProductMagentoSku): string
    {
        $formKey = $this->formKey->getFormKey();
        $actionUrl = $this->getUrl('*/*/*', ['action' => 'getRequestData']);

        return <<<HTML
<form style="margin: 0; font-size: 16px" method="get" enctype="multipart/form-data" action="$actionUrl">
    <input name="form_key" value="$formKey" type="hidden" />
    <input name="print" value="1" type="hidden" />

    <label style="display: inline-block;">
        Magento Product Sku:
        <input name="listing_product_magento_sku" style="width: 200px;" required value="$listingProductMagentoSku">
    </label>
    <div style="margin: 10px 0 0 0;">
        <button type="submit">Calculate Allowed Action</button>
    </div>
</form>
HTML;
    }

    private function printProductInfo(
        \M2E\Temu\Model\Product $listingProduct,
        \M2E\Temu\Model\Product\Action $action
    ): ?string {
        $calculateAction = 'Nothing';
        if ($action->isActionList()) {
            throw new \LogicException('Not implemented');
        } elseif ($action->isActionRevise()) {
            $calculateAction = sprintf(
                'Revise (Reason (%s))',
                implode(' | ', $action->getConfigurator()->getAllowedDataTypes()),
            );
            $request = $this->reviseRequestFactory->create();
            $printResult = $this->printRequestData(
                $request,
                $listingProduct,
                $action
            );
        } elseif ($action->isActionStop()) {
            $calculateAction = 'Stop';
            $request = $this->stopRequestFactory->create();
            $printResult = $this->printRequestData(
                $request,
                $listingProduct,
                $action
            );
        } elseif ($action->isActionRelist()) {
            $calculateAction = 'Relist';
            $request = $this->relistRequestFactory->create();
            $printResult = $this->printRequestData(
                $request,
                $listingProduct,
                $action
            );
        } else {
            $printResult = 'Nothing action allowed.';
        }
        $currentStatusTitle = \M2E\Temu\Model\Product::getStatusTitle($listingProduct->getStatus());

        $productSku = $listingProduct->getMagentoProduct()->getSku();

        $listingTitle = $listingProduct->getListing()->getTitle();

        return <<<HTML
<style>
    table {
      border-collapse: collapse;
      width: 100%;
    }

    td, th {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

</style>
<table>
    <tr>
        <td>Listing</td>
        <td>$listingTitle</td>
    </tr>
    <tr>
        <td>Product (SKU)</td>
        <td>$productSku</td>
    </tr>
    <tr>
        <td>Current Product Status</td>
        <td>$currentStatusTitle</td>
    </tr>
    <tr>
        <td>Calculate Action</td>
        <td>$calculateAction</td>
    </tr>
    <tr>
        <td>Request Data</td>
        <td>$printResult</td>
    </tr>
</table>
HTML;
    }

    private function printRequestData(
        \M2E\Temu\Model\Product\Action\AbstractRequest $request,
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action $action
    ): string {
        return sprintf(
            '<pre>%s</pre>',
            htmlspecialchars(
                json_encode(
                    $request->build(
                        $product,
                        $action->getConfigurator(),
                        $action->getVariantSettings(),
                        $this->logBufferFactory->create(),
                        []
                    )->toArray(),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
                ),
                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
            ),
        );
    }

    /**
     * @title "Build Order Quote"
     * @description "Print Order Quote Data"
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \M2E\Temu\Model\Exception
     * @throws \Throwable
     */
    public function getPrintOrderQuoteDataAction()
    {
        $isPrint = (bool)$this->getRequest()->getParam('print');
        $orderId = $this->getRequest()->getParam('order_id');

        $buildResultHtml = '';
        if ($isPrint && $orderId) {
            $orderResource = $this->_objectManager->create(\M2E\Temu\Model\ResourceModel\Order::class);
            $order = $this->_objectManager->create(\M2E\Temu\Model\Order::class);

            $orderResource->load($order, (int)$orderId);

            if (!$order->getId()) {
                $this->getMessageManager()->addErrorMessage('Unable to load order instance.');

                return $this->_redirect($this->controlPanelHelper->getPageModuleTabUrl());
            }

            // Store must be initialized before products
            // ---------------------------------------
            $order->associateWithStore();
            $order->associateItemsWithProducts();
            // ---------------------------------------

            $proxy = $order->getProxy()->setStore($order->getStore());

            $magentoQuoteBuilder = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\Magento\Quote\Builder::class, ['proxyOrder' => $proxy]);

            $magentoQuoteManager = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\Magento\Quote\Manager::class);

            $quote = $magentoQuoteBuilder->build();

            $shippingAddressData = $quote->getShippingAddress()->getData();
            unset(
                $shippingAddressData['cached_items_all'],
                $shippingAddressData['cached_items_nominal'],
                $shippingAddressData['cached_items_nonnominal'],
            );
            $billingAddressData = $quote->getBillingAddress()->getData();
            unset(
                $billingAddressData['cached_items_all'],
                $billingAddressData['cached_items_nominal'],
                $billingAddressData['cached_items_nonnominal'],
            );
            $quoteData = $quote->getData();
            unset(
                $quoteData['items'],
                $quoteData['extension_attributes'],
            );

            $items = [];
            foreach ($quote->getAllItems() as $item) {
                $items[] = $item->getData();
            }

            $magentoQuoteManager->save($quote->setIsActive(false));

            $buildResultHtml = json_encode(
                json_decode(
                    json_encode([
                        'Grand Total' => $quote->getGrandTotal(),
                        'Shipping Amount' => $quote->getShippingAddress()->getShippingAmount(),
                        'Quote Data' => $quoteData,
                        'Shipping Address Data' => $shippingAddressData,
                        'Billing Address Data' => $billingAddressData,
                        'Items' => $items,
                    ]),
                    true,
                ),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            );
        }

        $formKey = $this->formKey->getFormKey();
        $actionUrl = $this->getUrl('*/*/*', ['action' => 'getPrintOrderQuoteData']);

        $formHtml = <<<HTML
<form method="get" enctype="multipart/form-data" action="$actionUrl">
    <input name="form_key" value="{$formKey}" type="hidden" />
    <input name="print" value="1" type="hidden" />
    <div>
        <label>Order ID: </label>
        <input name="order_id" value="$orderId" required>
        <button type="submit">Build</button>
    </div>
</form>
HTML;
        $resultHtml = $formHtml;
        if ($buildResultHtml !== '') {
            $resultHtml .= "<h3>Result</h3><div><pre>$buildResultHtml</pre></div>";
        }

        return $resultHtml;
    }

    /**
     * @title "Print Inspector Data"
     * @description "Print Inspector Data"
     * @new_line
     */
    public function getInspectorDataAction()
    {
        if ($this->getRequest()->getParam('print')) {
            $listingProductId = $this->getRequest()->getParam('listing_product_id');

            $listingProduct = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\Product\Repository::class)
                ->get($listingProductId);

            $instructionCollection = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\ResourceModel\Instruction\CollectionFactory::class)
                ->create();

            $instructionCollection->addFieldToFilter('listing_product_id', $listingProductId);

            $instructions = [];
            foreach ($instructionCollection->getItems() as $instruction) {
                $instruction->setListingProduct($listingProduct);
                $instructions[$instruction->getId()] = $instruction;
            }

            $checkerInput = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\InputFactory::class)
                ->create($listingProduct, $instructions);

            $html = '<pre>';

            $notListedChecker = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\CheckerFactory::class)
                ->create(
                    \M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\NotListedChecker::class,
                    $checkerInput,
                );

            $html .= '<b>NotListed</b><br>';
            $html .= 'isAllowed: ' . json_encode($notListedChecker->isAllowed()) . '<br>';

            $inactiveChecker = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\CheckerFactory::class)
                ->create(
                    \M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\InactiveChecker::class,
                    $checkerInput,
                );

            $html .= '<b>Inactive</b><br>';
            $html .= 'isAllowed: ' . json_encode($inactiveChecker->isAllowed()) . '<br>';

            $activeChecker = $this
                ->_objectManager
                ->create(\M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\CheckerFactory::class)
                ->create(
                    \M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\ActiveChecker::class,
                    $checkerInput,
                );

            $html .= '<b>Active</b><br>';
            $html .= 'isAllowed: ' . json_encode($activeChecker->isAllowed()) . '<br>';

            $magentoProduct = $listingProduct->getMagentoProduct();
            $html .= 'isStatusEnabled: ' . json_encode($magentoProduct->isStatusEnabled()) . '<br>';
            $html .= 'isStockAvailability: ' . json_encode($magentoProduct->isStockAvailability()) . '<br>';

            //--

            return $this->getResponse()->setBody($html);
        }

        $formKey = $this->formKey->getFormKey();
        $actionUrl = $this->getUrl('*/*/*', ['action' => 'getInspectorData']);

        return <<<HTML
<form method="get" enctype="multipart/form-data" action="{$actionUrl}">

    <div style="margin: 5px 0; width: 400px;">
        <label style="width: 170px; display: inline-block;">Listing Product ID: </label>
        <input name="listing_product_id" style="width: 200px;" required>
    </div>

    <input name="form_key" value="{$formKey}" type="hidden" />
    <input name="print" value="1" type="hidden" />

    <div style="margin: 10px 0; width: 365px; text-align: right;">
        <button type="submit">Show</button>
    </div>

</form>
HTML;
    }
}
