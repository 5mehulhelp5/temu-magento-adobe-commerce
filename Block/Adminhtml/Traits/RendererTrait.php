<?php

namespace M2E\Temu\Block\Adminhtml\Traits;

trait RendererTrait
{
    /** @var \M2E\Temu\Block\Adminhtml\Magento\Renderer\JsPhpRenderer */
    public $jsPhp;

    /** @var \M2E\Temu\Block\Adminhtml\Magento\Renderer\JsTranslatorRenderer */
    public $jsTranslator;

    /** @var \M2E\Temu\Block\Adminhtml\Magento\Renderer\JsUrlRenderer */
    public $jsUrl;

    /** @var \M2E\Temu\Block\Adminhtml\Magento\Renderer\JsRenderer */
    public $js;

    /** @var \M2E\Temu\Block\Adminhtml\Magento\Renderer\CssRenderer */
    public $css;
}
