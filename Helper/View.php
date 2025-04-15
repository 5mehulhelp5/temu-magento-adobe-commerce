<?php

namespace M2E\Temu\Helper;

class View
{
    public const LISTING_CREATION_MODE_FULL = 0;
    public const LISTING_CREATION_MODE_LISTING_ONLY = 1;

    public const MOVING_LISTING_OTHER_SELECTED_SESSION_KEY = 'moving_listing_other_selected';
    public const MOVING_LISTING_PRODUCTS_SELECTED_SESSION_KEY = 'moving_listing_products_selected';

    private View\Temu $viewHelper;
    private View\Temu\Controller $controllerHelper;
    private \Magento\Framework\App\RequestInterface $request;

    public function __construct(
        \M2E\Temu\Helper\View\Temu $viewHelper,
        \M2E\Temu\Helper\View\Temu\Controller $controllerHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->viewHelper = $viewHelper;
        $this->controllerHelper = $controllerHelper;
        $this->request = $request;
    }

    public function getViewHelper(): View\Temu
    {
        return $this->viewHelper;
    }

    public function getControllerHelper(): View\Temu\Controller
    {
        return $this->controllerHelper;
    }

    public function getCurrentView(): ?string
    {
        $controllerName = $this->request->getControllerName();
        if ($controllerName === null) {
            return null;
        }

        if (stripos($controllerName, 'system_config') !== false) {
            return \M2E\Temu\Helper\View\Configuration::NICK;
        }

        return \M2E\Temu\Helper\View\Temu::NICK;
    }

    // ---------------------------------------

    public function getModifiedLogMessage($logMessage)
    {
        return \M2E\Core\Helper\Data::escapeHtml(
            \M2E\Temu\Helper\Module\Log::decodeDescription($logMessage),
            ['a', 'b'],
            ENT_NOQUOTES
        );
    }
}
