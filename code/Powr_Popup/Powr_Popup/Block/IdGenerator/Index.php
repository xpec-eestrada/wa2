<?php
namespace Powr\Popup\Block\IdGenerator;

class Index extends \Magento\Backend\Block\Template{
    protected $_elementFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }
    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if($element->hasValue()){
            $id = $element->getValue();
        } else {
            $id = $this->getRandomId();
        }

        if(empty($id)){
            $id = $this->getRandomId();
        }
        
        $html = '<input id="'.$element->getHtmlId().'" name="'.$element->getName().'" value="'.$id.'" class="widget-option input-text" type="text">';

        $element->setData('after_element_html', $html);
        return $element;
    }


    public function getRandomId(){
        $randomValuesArray = $this->generateArrayOfRandomValues();
        $randomString = $this->addCurrentTimeToAvoidDuplicateKeysAndConvertToString($randomValuesArray);
        return $randomString;
    }

    private function addCurrentTimeToAvoidDuplicateKeysAndConvertToString($pass){
        return implode($pass) . '_' .time();
    }

    private function generateArrayOfRandomValues(){
        $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
        $randomArray = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $randomArray[] = $alphabet[$n];
        }
        return $randomArray;
    }


}
