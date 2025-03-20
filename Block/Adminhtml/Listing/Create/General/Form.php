<?php

namespace M2E\Temu\Block\Adminhtml\Listing\Create\General;

use M2E\Temu\Block\Adminhtml\StoreSwitcher;
use M2E\Temu\Model\Listing;

class Form extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm
{
    private \M2E\Core\Helper\Magento\Store $storeHelper;
    protected Listing $listing;
    private \M2E\Temu\Helper\Data\Session $sessionDataHelper;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private Listing\Repository $listingRepository;
    private \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper;
    private \M2E\Temu\Model\Account\Ui\UrlHelper $urlHelper;

    public function __construct(
        \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper,
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Core\Helper\Magento\Store $storeHelper,
        \M2E\Temu\Model\Account\Ui\UrlHelper $urlHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \M2E\Temu\Helper\Data\Session $sessionDataHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->storeHelper = $storeHelper;
        $this->sessionDataHelper = $sessionDataHelper;
        $this->accountRepository = $accountRepository;
        $this->listingRepository = $listingRepository;
        $this->accountUrlHelper = $accountUrlHelper;
        $this->urlHelper = $urlHelper;
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => 'javascript:void(0)',
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General'),
                'collapsable' => false,
            ]
        );

        $title = $this->getTitle();
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'value' => $title,
                'required' => true,
                'class' => 'Temu-listing-title',
                'tooltip' => __(
                    'Create a descriptive and meaningful Title for your %extension_title ' .
                    'Listing. <br/> This is used for reference within %extension_title and will not appear on ' .
                    'your %channel_title Listings.',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
            ]
        );

        $fieldset = $form->addFieldset(
            'temu_settings_fieldset',
            [
                'legend' => __(\M2E\Temu\Helper\Module::getChannelTitle() . ' Settings'),
                'collapsable' => false,
            ]
        );

        $accountsData = $this->getAccountData();
        if ($accountsData['select_account_is_disabled']) {
            $fieldset->addField(
                'account_id_hidden',
                'hidden',
                [
                    'name' => 'account_id',
                    'value' => $accountsData['active_account_id'],
                ]
            );
        }

        $accountSelect = $this->elementFactory->create(
            self::SELECT,
            [
                'data' => [
                    'html_id' => 'account_id',
                    'name' => 'account_id',
                    'style' => 'width: 50%;',
                    'value' => $accountsData['active_account_id'],
                    'values' => $accountsData['accounts'],
                    'required' => $accountsData['is_required'],
                    'disabled' => $accountsData['select_account_is_disabled'],
                ],
            ]
        );
        $accountSelect->setForm($form);

        $isAddAccountButtonHidden = $this->getRequest()->getParam('wizard', false)
            || $accountsData['select_account_is_disabled'];

        $addAnotherAccountButton = $this
            ->getLayout()
            ->createBlock(
                \M2E\Temu\Block\Adminhtml\Magento\Button\DropDown::class
            );

        $addAnotherAccountButton->setData([
            'id' => 'add_account',
            'label' => __('Add Another'),
            'class' => 'primary',
            'options' => $this->getDropdownOptions()
        ]);

        $fieldset->addField(
            'account_container',
            self::CUSTOM_CONTAINER,
            [
                'label' => __('Account'),
                'style' => 'line-height: 32px; display: initial;',
                'required' => $accountsData['is_required'],
                'text' => <<<HTML
    <span id="account_label"></span>
    {$accountSelect->toHtml()}
HTML
                ,
                'after_element_html' => sprintf(
                    '<div style="margin-left:5px; display: inline-block; position:absolute;%s">%s</div>',
                    $isAddAccountButtonHidden ? 'display: none;' : '',
                    $addAnotherAccountButton->toHtml()
                ),
            ]
        );

        $fieldset = $form->addFieldset(
            'magento_fieldset',
            [
                'legend' => __('Magento Settings'),
                'collapsable' => false,
            ]
        );

        $storeId = $this->getSessionData('store_id') ?? $this->storeHelper->getDefaultStoreId();
        $fieldset->addField(
            'store_id',
            self::STORE_SWITCHER,
            [
                'name' => 'store_id',
                'label' => __('Magento Store View'),
                'value' => $storeId,
                'required' => true,
                'has_empty_option' => true,
                'tooltip' => __(
                    'Choose the Magento Store View you want to use for this %extension_title ' .
                    'Listing. Please remember that Attribute values from the selected Store View will ' .
                    'be used in the Listing.',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    ]
                ),
                'display_default_store_mode' => StoreSwitcher::DISPLAY_DEFAULT_STORE_MODE_DOWN,
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function getTitle(): string
    {
        if ($fromSession = $this->getSessionData('title')) {
            return $fromSession;
        }

        return $this->listingRepository->getListingsCount() === 0
            ? (string)__('Default')
            : '';
    }

    /**
     * @return array{
     *     account_is_disabled: bool,
     *     is_required: bool,
     *     active_account_id: int,
     *     accounts: array
     * }
     */
    private function getAccountData(): array
    {
        $accounts = $this->accountRepository->getAll();

        if ($accounts === []) {
            return [
                'select_account_is_disabled' => false,
                'is_required' => 0,
                'active_account_id' => 0,
                'accounts' => [],
            ];
        }

        $data = [
            'select_account_is_disabled' => false,
            'is_required' => count($accounts) > 1,
            'active_account_id' => reset($accounts)->getId(),
            'accounts' => array_map(
                static function (\M2E\Temu\Model\Account $account) {
                    return [
                        'value' => $account->getId(),
                        'label' => $account->getTitle(),
                    ];
                },
                $accounts
            ),
        ];

        if ($sessionAccountId = $this->getSessionData('account_id')) {
            $data['active_account_id'] = $sessionAccountId;

            return $data;
        }

        if ($requestAccountId = $this->getRequest()->getParam('account_id')) {
            $data['select_account_is_disabled'] = true;
            $data['active_account_id'] = (int)$requestAccountId;
        }

        return $data;
    }

    protected function _prepareLayout()
    {

        $this->jsUrl->addUrls([
            'account/accountList' => $this->urlHelper->getAccountListUrl(),
            'account/create' => $this->urlHelper->getCreateUrl(),
            'account/delete' => $this->getUrl(\M2E\Temu\Model\Account\Ui\UrlHelper::PATH_DELETE),
            'account/edit' => $this->getUrl(\M2E\Temu\Model\Account\Ui\UrlHelper::PATH_EDIT),
            'account/index' => $this->urlHelper->getIndexUrl(),
            'account/refresh' => $this->getUrl(\M2E\Temu\Model\Account\Ui\UrlHelper::PATH_REFRESH),
            'account/save' => $this->getUrl(\M2E\Temu\Model\Account\Ui\UrlHelper::PATH_SAVE),
            'account/updateCredentials' => $this->getUrl(\M2E\Temu\Model\Account\Ui\UrlHelper::PATH_UPDATE_CREDENTIALS),
        ]);

        $this->jsUrl->add(
            $this->getUrl(
                '*/temu_synchronization_log/index',
                [
                    'wizard' => (bool)$this->getRequest()->getParam('wizard', false),
                ]
            ),
            'logViewUrl'
        );

        $urlListingCreate = $this->getUrl('*/listing_create/index', ['_current' => true]);
        $urlAccountList = $this->accountUrlHelper->getAccountListUrl();
        $this->js->addOnReadyJs(
            <<<JS
    require([
        'Temu/Listing/Create/General'
    ], function(){
        Temu.formData.wizard = {$this->getRequest()->getParam('wizard', 0)};

        window.TemuListingCreateGeneralObj = new TemuListingCreateGeneral('$urlListingCreate', '$urlAccountList');
    });
JS
        );

        return parent::_prepareLayout();
    }

    private function getSessionData(string $key): ?string
    {
        $sessionData = $this->sessionDataHelper->getValue(Listing::CREATE_LISTING_SESSION_DATA);

        return $sessionData[$key] ?? null;
    }

    private function getDropdownOptions(): array
    {
        return [
            [
                'label' => 'US',
                'id' => 'US',
                'onclick' => 'setLocation(this.getAttribute("data-url"))',
                'data_attribute' => [
                    'url' => $this->accountUrlHelper->getBeforeGetTokenUrl(
                        ['_current' => true, 'region' => 'US', 'specific_end_url' => $this->getUrl('*/*/*', ['_current' => true]),]
                    ),
                ],
            ],
            [
                'label' => 'EU',
                'id' => 'EU',
                'onclick' => 'setLocation(this.getAttribute("data-url"))',
                'data_attribute' => [
                    'url' => $this->accountUrlHelper->getBeforeGetTokenUrl(
                        ['_current' => true, 'region' => 'EU', 'specific_end_url' => $this->getUrl('*/*/*', ['_current' => true]),]
                    ),
                ],
            ]
        ];
    }
}
