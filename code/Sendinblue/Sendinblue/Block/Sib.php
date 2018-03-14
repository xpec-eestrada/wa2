<?php
namespace Sendinblue\Sendinblue\Block;

class Sib extends \Magento\Framework\View\Element\Template
{
    public $_config;
    public $_adminSampleModel;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getDataDb()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dbConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $dbConfig;
    }
    public function getFormValue()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        return $FormKey->getFormKey();
    }
}
