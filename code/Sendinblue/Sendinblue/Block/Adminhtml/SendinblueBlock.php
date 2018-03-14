<?php
namespace Sendinblue\Sendinblue\Block\Adminhtml;

use Magento\Backend\Block\Template;

class SendinblueBlock extends Template
{
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getDataDb()
    {
        $objectManager = $this->getObjValue();
        $dbConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $dbConfig;
    }
    public function getFormValue()
    {
        $objectManager = $this->getObjValue();
        $FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        return $FormKey->getFormKey();
    }

    public function getObjValue()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }
}
