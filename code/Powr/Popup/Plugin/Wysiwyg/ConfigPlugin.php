<?php

namespace Powr\Popup\Plugin\Wysiwyg;

use Magento\Cms\Model\Wysiwyg\Config as Subject;
use Magento\Framework\DataObject;
use Powr\Popup\Model\Wysiwyg\Powr;

class ConfigPlugin
{
    /**
     * @var Powr
     */
    private $powrpopup;

    /**
     * ConfigPlugin constructor.
     * @param Powr $powr
     */
    public function __construct(
        Powr $powrpopup
    ) {
        $this->powrpopup = $powrpopup;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(Subject $subject, DataObject $config) // DataObject
    {
        $powrpopupPluginSettings = $this->powrpopup->getPluginSettings($config);
        $config->addData($powrpopupPluginSettings);
        return $config;
    }
}
