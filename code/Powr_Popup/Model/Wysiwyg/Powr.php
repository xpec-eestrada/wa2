<?php

namespace Powr\Popup\Model\Wysiwyg;

use Magento\Framework\DataObject;

class Powr
{
    const PLUGIN_NAME = 'powrpopup';

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * Powr constructor.
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;
    }

    public function getPluginSettings(DataObject $config) // array
    {
        $plugins = $config->getData('plugins');
        $plugins[] = [
                'name' => self::PLUGIN_NAME,
                'src' => $this->getPluginJsSrc(),
                'options' => [
                    'title' => __('POWr Popup'),
                    'class' => 'add-powrpopup plugin',
                    'css' => $this->getPluginCssSrc()
                ]
            ];

        return ['plugins' => $plugins];
    }

    private function getPluginJsSrc() // string
    {
        return $this->assetRepo->getUrl(
            sprintf('Powr_Popup::js/tiny_mce/plugins/%s/editor_plugin.js', self::PLUGIN_NAME)
        );
    }

    private function getPluginCssSrc() // string
    {
        return $this->assetRepo->getUrl(
            sprintf('Powr_Popup::css/tiny_mce/plugins/%s/content.css', self::PLUGIN_NAME)
        );
    }
}
