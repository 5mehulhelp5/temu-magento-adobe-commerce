<?php

namespace M2E\Temu\Plugin\Menu\Magento\Backend\Model\Menu;

use M2E\Temu\Helper\View;

class Item extends \M2E\Temu\Plugin\AbstractPlugin
{
    /** @var array */
    private $menuTitlesUsing = [];

    /** @var \M2E\Temu\Helper\View\Temu */
    protected $view;
    /** @var \M2E\Temu\Helper\Module\Wizard */
    private $wizardHelper;

    public function __construct(
        \M2E\Temu\Helper\Module\Wizard $wizardHelper,
        View\Temu $view
    ) {
        $this->wizardHelper = $wizardHelper;
        $this->view = $view;
    }

    /**
     * @param \Magento\Backend\Model\Menu\Item $interceptor
     * @param \Closure $callback
     *
     * @return string
     */
    public function aroundGetClickCallback($interceptor, \Closure $callback, ...$arguments)
    {
        return $this->execute('getClickCallback', $interceptor, $callback, $arguments);
    }

    protected function processGetClickCallback($interceptor, \Closure $callback, array $arguments)
    {
        $id = $interceptor->getId();
        $urls = $this->getUrls();

        if (isset($urls[$id])) {
            return $this->renderOnClickCallback($urls[$id]);
        }

        return $callback(...$arguments);
    }

    /**
     * Gives able to display titles in menu slider which differ from titles in menu panel
     *
     * @param \Magento\Backend\Model\Menu\Item $interceptor
     * @param \Closure $callback
     *
     * @return string
     */
    public function aroundGetTitle($interceptor, \Closure $callback, ...$arguments)
    {
        return $this->execute('getTitle', $interceptor, $callback, $arguments);
    }

    protected function processGetTitle($interceptor, \Closure $callback, array $arguments)
    {
        if (
            $interceptor->getId() == View\Temu::MENU_ROOT_NODE_NICK
            && !isset($this->menuTitlesUsing[View\Temu::MENU_ROOT_NODE_NICK])
        ) {
            $wizard = $this->wizardHelper->getActiveWizard(
                View\Temu::NICK
            );

            if ($wizard === null) {
                $this->menuTitlesUsing[View\Temu::MENU_ROOT_NODE_NICK] = true;

                return 'Temu';
            }
        }

        return $callback(...$arguments);
    }

    private function getUrls()
    {
        return [
            'M2E_Temu::temu_help_center_knowledge_base'
            => 'https://help.m2epro.com',
        ];
    }

    private function renderOnClickCallback($url)
    {
        return "window.open('$url', '_blank'); return false;";
    }
}
