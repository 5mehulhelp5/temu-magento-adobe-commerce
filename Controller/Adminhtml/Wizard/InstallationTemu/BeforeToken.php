<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

class BeforeToken extends Installation
{
    private \M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrl\Processor $getGrantAccessUrlProcessor;
    private \M2E\Temu\Helper\View\Configuration $configurationHelper;
    private \M2E\Temu\Helper\Module\Exception $exceptionHelper;
    private \M2E\Core\Model\LicenseService $licenseService;

    public function __construct(
        \M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrl\Processor $getGrantAccessUrlProcessor,
        \M2E\Temu\Helper\Module\Exception $exceptionHelper,
        \M2E\Temu\Helper\View\Configuration $configurationHelper,
        \M2E\Core\Helper\Magento $magentoHelper,
        \M2E\Temu\Helper\Module\Wizard $wizardHelper,
        \Magento\Framework\Code\NameBuilder $nameBuilder,
        \M2E\Core\Model\LicenseService $licenseService
    ) {
        parent::__construct(
            $magentoHelper,
            $wizardHelper,
            $nameBuilder,
            $licenseService,
        );

        $this->getGrantAccessUrlProcessor = $getGrantAccessUrlProcessor;
        $this->configurationHelper = $configurationHelper;
        $this->exceptionHelper = $exceptionHelper;
        $this->licenseService = $licenseService;
    }

    public function execute()
    {
        try {
            $region = $this->getRequest()->getParam('region');
            $backUrl = $this->getUrl('*/*/afterToken', [
                'region' => $region,
            ]);

            $response = $this->getGrantAccessUrlProcessor->processAddAccount($backUrl, $region);
        } catch (\Throwable $exception) {
            $this->exceptionHelper->process($exception);

            if (
                !$this->licenseService->get()->getInfo()->getDomainIdentifier()->isValid()
                || !$this->licenseService->get()->getInfo()->getIpIdentifier()->isValid()
            ) {
                $error = __(
                    'The Temu token obtaining is currently unavailable.<br/>Reason: %error_message' .
                    '</br>Go to the <a href="%url" target="_blank">License Page</a>.',
                    [
                        'error_message' => $exception->getMessage(),
                        'url' => $this->configurationHelper->getLicenseUrl(['wizard' => 1]),
                    ],
                );
            } else {
                $error = __(
                    'The Temu token obtaining is currently unavailable.<br/>Reason: %error_message',
                    ['error_message' => $exception->getMessage()]
                );
            }

            $this->setJsonContent([
                'type' => 'error',
                'message' => $error,
            ]);

            return $this->getResult();
        }

        $this->setJsonContent([
            'url' => $response->getUrl(),
        ]);

        return $this->getResult();
    }
}
