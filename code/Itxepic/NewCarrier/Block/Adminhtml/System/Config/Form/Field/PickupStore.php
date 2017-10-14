<?php
namespace Itxepic\NewCarrier\Block\Adminhtml\System\Config\Form\Field;

class PickupStore extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    
    protected $_elementFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    )
    {
        $this->_elementFactory  = $elementFactory;
        parent::__construct($context,$data);
    }
    protected function _construct()
	{
        $this->addColumn('pick_store', ['label' => __('Store Name')]);
        //$this->addColumn('maxqty', ['label' => __('Max Qty')]);
        //$this->addColumn('sort_order', ['label' => __('Sort Order')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }

}