<?php

namespace Itxepic\NewCarrier\Observer;

use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Itxepic\NewCarrier\Helper\Data $dataHelper
    ) {
        $this->_objectManager = $objectmanager;
        $this->_checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $enable = $this->dataHelper->isActive();
        if ($enable == 1) {
            if ($order->getShippingMethod(true)->getCarrierCode() == "newcarrier") {
                $quoteRepository = $this->_objectManager
                    ->create('Magento\Quote\Model\QuoteRepository');
                $quote = $quoteRepository->get($order->getQuoteId());
                $order->setStorePickup($quote->getStorePickup());
            }
        }
    }

}