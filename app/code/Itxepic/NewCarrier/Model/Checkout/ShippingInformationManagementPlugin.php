<?php

namespace Itxepic\NewCarrier\Model\Checkout;

class ShippingInformationManagementPlugin
{

    protected $quoteRepository;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $storePickup = $extAttributes->getStorePickup();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setStorePickup($storePickup);
    }
}