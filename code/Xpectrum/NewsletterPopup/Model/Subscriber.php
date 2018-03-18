<?php

namespace Xpectrum\NewsletterPopup\Model;


class Subscriber extends \Magento\Newsletter\Model\Subscriber
{

    public $couponManagementService;
    public $generationSpecFactory;
    public $ruleId = 13;

    public function sendConfirmationSuccessEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if (!$this->_scopeConfig->getValue(
            self::XML_PATH_SUCCESS_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) || !$this->_scopeConfig->getValue(
            self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return $this;
        }

        $tmp = $this->_scopeConfig->getValue('xpecnewsletter/general/id_cupon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->ruleId = (isset($tmp) && is_numeric($tmp) && $tmp>0)?$tmp:$this->ruleId;
        $this->inlineTranslation->suspend();
        $objectManager                  = \Magento\Framework\App\ObjectManager::getInstance();
        $this->couponManagementService  = $objectManager->get('Magento\SalesRule\Model\Service\CouponManagementService');
        $this->generationSpecFactory    = $objectManager->get('Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory');
        $cupon                          = $this->generate($this->ruleId);
        $resource                       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection                     = $resource->getConnection();
        $tableName                      = $resource->getTableName('xpec_email_cupon_newsletter');

        $sql    = "INSERT INTO ".$tableName."(email,cupon,estado) VALUES('".$this->getEmail()."','".$cupon."','A')";
        $connection->query($sql);

        $this->_transportBuilder->setTemplateIdentifier(
            $this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            ['subscriber' => $this,'xpec_cupon' => $cupon]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $this->getEmail(),
            $this->getName()
        );
        
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();

        return $this;
    }
    
    public function generate($ruleId)
    {
        $data = array(
            "rule_id" => $ruleId,
            "quantity" => 1,
            'length' => 8,
            'format' => 'alphanum'
        );
        
        $couponSpec = $this->generationSpecFactory->create(['data' => $data]);
        $couponCodes = $this->couponManagementService->generate($couponSpec);
        return (isset($couponCodes) && is_array($couponCodes) && isset($couponCodes[0])) ? $couponCodes[0] : '';
    }
}
