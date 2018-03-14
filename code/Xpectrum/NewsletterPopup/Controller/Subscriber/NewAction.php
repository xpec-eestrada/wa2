<?php

namespace Xpectrum\NewsletterPopup\Controller\Subscriber;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use \Xpectrum\NewsletterPopup\Logger\Logger;

class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{

    const COOKIE_NAME = 'xpec-newsletter';
    const COOKIE_DURATION = 86400;

    protected $_cookieManager;
    protected $_cookieMetadataFactory;
    protected $_storeManager;
    protected $logger;
    protected $resultJsonFactory;
    
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        JsonFactory $resultJsonFactory,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
            $customerAccountManagement
        );
    }

    public function execute()
    {        
        $response = [];
        
        if ($this->getRequest()->getPost('email')) {            
            $email = (string)$this->getRequest()->getPost('email');
            
            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $tableName = $resource->getTableName('newsletter_subscriber');
                $store_id = $this->_storeManager->getStore()->getId();
                
                $sql = 'SELECT subscriber_id FROM ' . $tableName . '
                        WHERE subscriber_email = \'' . $email . '\' AND subscriber_status = 1 AND store_id = ' . $store_id;
                $result = $connection->fetchOne($sql);                
                $issubscriber = (isset($result) && is_numeric($result) && $result > 0) ? true : false;
                
                if ($issubscriber) {
                    $response = [
                        'status' => 'ERROR',
                        'msg' => __('Este correo ya esta registrado.'),
                    ];
                    $this->logger->error('Error: Este correo ya esta registrado.');
                } else {
                    $status = $this->_subscriberFactory->create()->subscribe($email);
                    if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                        $response = [
                            'status' => 'OK',
                            'msg' => 'La confirmación de la solicitud ha sido enviada.',
                        ];
                    } else {
                        $response = [
                            'status' => 'OK',
                            'msg' => 'Gracias por suscribirte.',
                        ];
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = [
                    'status' => 'ERROR',
                    'msg' => __('Hubo un error con la suscripción: %1', $e->getMessage()),
                ];
                $this->logger->critical($e->getMessage());
            } catch (\Exception $e) {                
                $response = [
                    'status' => 'ERROR',
                    'msg' => __('Ocurrión un error con la suscripción.'),
                ];
                $this->logger->critical($e->getMessage());
            }
        } else {
            $response = [
                'status' => 'ERROR',
                'msg' => __('El correo es obligatorio'),
            ];
        }
        
        return $this->resultJsonFactory->create()->setData($response);
    }

    public function set($value, $duration = 86400)
    {        
        $metadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath($this->_sessionManager->getCookiePath())
            ->setDomain($this->_storeManager->getStore()->getBaseUrl());
 
        $this->_cookieManager->setPublicCookie(
            $this->getRemoteAddress(),
            $value,
            $metadata
        );
    }
}
