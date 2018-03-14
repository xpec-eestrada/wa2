<?php

namespace MagicToolbox\MagicZoom\Observer;

use Magento\Framework\Event\ObserverInterface;
use MagicToolbox\MagicZoom\Helper\Data;

/**
 * MagicToolbox Observer
 *
 */
class FixLayoutAfter implements ObserverInterface
{
    /**
     * Helper
     *
     * @var \MagicToolbox\MagicZoom\Helper\Data
     */
    public $magicToolboxHelper = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Constructor
     *
     * @param \MagicToolbox\MagicZoom\Helper\Data $magicToolboxHelper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \MagicToolbox\MagicZoom\Helper\Data $magicToolboxHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->magicToolboxHelper = $magicToolboxHelper;
        $this->coreRegistry = $registry;
    }

    /**
     * Execute method
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     *
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();

        $block = $layout->getBlock('product.info.media.magiczoom');
        if ($block) {
            $data = $this->coreRegistry->registry('magictoolbox');
            if (is_null($data)) {
                $data = [
                    'current' => '',
                    'blocks' => [
                        'product.info.media.magic360' => null,
                        'product.info.media.magicslideshow' => null,
                        'product.info.media.magicscroll' => null,
                        'product.info.media.magiczoomplus' => null,
                        'product.info.media.magiczoom' => null,
                        'product.info.media.magicthumb' => null,
                        'product.info.media.image' => null,
                    ],
                    'cooperative-mode' => false,
                    'additional-block-name' => '',
                    'magicscroll' => '',
                ];
            }

            if (empty($data['current'])) {
                $original = $layout->getBlock('product.info.media.image');
                if ($original) {
                    $data['current'] = 'product.info.media.image';
                    $data['blocks']['product.info.media.image'] = $original;
                }
            }

            $magiczoom = $this->magicToolboxHelper->getToolObj();
            $isEnabled = !$magiczoom->params->checkValue('enable-effect', 'No', 'product');

            if ($isEnabled) {
                $layout->unsetElement($data['current']);
                $data['current'] = 'product.info.media.magiczoom';
                $data['blocks']['product.info.media.magiczoom'] = $block;
                //NOTE: to determine which module will display magicscroll headers on the product page
                $data['magicscroll'] = 'magiczoom';
            } else {
                $layout->unsetElement('product.info.media.magiczoom');
            }
            $this->coreRegistry->unregister('magictoolbox');
            $this->coreRegistry->register('magictoolbox', $data);
        }

        $block = $layout->getBlock('configurable.magiczoom');
        if ($block) {

            $data = $this->coreRegistry->registry('magictoolbox_category');
            if (is_null($data)) {
                $data = [
                    'current-renderer' => '',
                    'renderers' => [
                        'configurable.magic360' => null,
                        'configurable.magicslideshow' => null,
                        'configurable.magicscroll' => null,
                        'configurable.magiczoomplus' => null,
                        'configurable.magiczoom' => null,
                        'configurable.magicthumb' => null,
                        //'configurable' => null,
                    ],
                ];
            }

            /** @var $rendererList \Magento\Framework\View\Element\RendererList */
            $rendererList = $layout->getBlock('category.product.type.details.renderers');

            if (empty($data['current-renderer'])) {
                if ($rendererList) {
                    /** @var $block \Magento\Swatches\Block\Product\Renderer\Listing\Configurable */
                    $renderer = $rendererList->getChildBlock('configurable');
                    if ($renderer) {
                        $name = $renderer->getNameInLayout();
                        $data['current-renderer'] = $name;
                        $data['renderers'][$name] = $renderer;
                    }
                }
            }

            $magiczoom = $this->magicToolboxHelper->getToolObj();
            $isEnabled = $magiczoom->params->checkValue('enable-effect', 'Yes', 'category');
            if ($isEnabled) {
                if ($rendererList) {
                    $rendererList->setChild('configurable', $block);
                }
                $data['current-renderer'] = 'configurable.magiczoom';
                $data['renderers']['configurable.magiczoom'] = $block;
            } else {
                $layout->unsetElement('configurable.magiczoom');
            }
            $this->coreRegistry->unregister('magictoolbox_category');
            $this->coreRegistry->register('magictoolbox_category', $data);
        }

        return $this;
    }
}
