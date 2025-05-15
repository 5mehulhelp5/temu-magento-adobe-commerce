<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Account;

class CredentialsFormFactory
{
    private \Magento\Framework\Data\FormFactory $formFactory;

    public function __construct(
        \Magento\Framework\Data\FormFactory $formFactory
    ) {
        $this->formFactory = $formFactory;
    }

    public function create(
        string $id,
        string $submitUrl = ''
    ): \Magento\Framework\Data\Form {
        $form = $this->formFactory->create(
            [
                'data' => [
                    'id' => $id,
                    'action' => $submitUrl,
                    'method' => 'post',
                ],
            ]
        );

        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'general_credentials',
            [],
        );

        $fieldset->addField(
            'region',
            'select',
            [
                'name' => 'region',
                'label' => __('Select type of the Account you would like to connect:'),
                'values' => [
                    'US' => __('US'),
                    'EU' => __('EU'),
                    'global' => __('Global'),
                ],
            ]
        );

        return $form;
    }
}
