<?php

namespace Itxepic\NewCarrier\Block;

class Storepickup extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Itxepic\NewCarrier\Helper\Data $dataHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->datahelper = $dataHelper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    public function getPickupCollection()
    {
        $pickup = $this->datahelper->getPickUpStores();
		/* echo '<pre>';
		print_r($pickup);
		exit; */
        return $this->jsonHelper->jsonEncode($pickup);
    }
}