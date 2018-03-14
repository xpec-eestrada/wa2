<?php
namespace Xpectrum\NewsletterPopup\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;

class AjaxSubscribe extends \Magento\Framework\App\Action\Action{

    protected $_resultPageFactory;
    protected $_resource;
    protected $subscriberFactory;
    protected $_storeManager;
    protected $_customerSession;
    protected $customerAccountManagement;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        CustomerAccountManagement $customerAccountManagement
    ) {
        $this->customerAccountManagement    = $customerAccountManagement;
        $this->_customerSession             = $customerSession;
        $this->_storeManager                = $storeManager;
        $this->subscriberFactory            = $subscriberFactory;
        $this->_resultPageFactory           = $resultJsonFactory;
        $this->_resource                    = $resource;
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return $this
     */
    public function execute(){
        $post       = $this->getRequest()->getPostValue();
        $datajson   = $this->_resultPageFactory->create();
        try{
            $email = $post['email'];
            if(!empty($email)){
                $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
                $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection     = $resource->getConnection();
                $tableName      = $resource->getTableName('newsletter_subscriber');
                $tablecoupon    = $resource->getTableName('xpec_email_cupon_newsletter');
                $store_id       = $this->_storeManager->getStore()->getWebsiteId();
                $sql = 'SELECT subscriber_id FROM ' . $tableName . '
                        WHERE subscriber_email = \'' . $email . '\' AND subscriber_status = 1 AND store_id = ' . $store_id;
                $result = $connection->fetchOne($sql);
                $issubscriber = (isset($result) && is_numeric($result) && $result > 0) ? true : false;
                $sql    = "SELECT cupon FROM ".$tablecoupon." WHERE email = '".$email."' ";
                $cupon  = $connection->fetchOne($sql);
                if(!$issubscriber){
                    if($this->validateEmailFormatXpec($email)){
                        $this->validateEmailAvailable($email);
                        $status = $this->subscriberFactory->create()->subscribe($email);
                        
                        if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                            $response = [
                                'status'    => 'OK',
                                'code'      => 101,
                                'msg'       => 'No esta activo.'
                            ];
                        } else {
                            $response = [
                                'status'    => 'OK',
                                'code'      => 102,
                                'msg'       => 'Se ha suscrito con exito.',
                                'cupon'     => $cupon
                            ];
                        }
                    }
                }else{
                    $response = [
                        'status' => 'NO',
                        'code'  => 104,
                        'cupon'     => $cupon,
                        'msg' => 'Email ya esta registrado.',
                    ];
                }
            }else{
                $response = [
                    'status' => 'NO',
                    'code'  => 103,
                    'msg' => 'Email no puede estar vacio.',
                ];
            }
        }catch(\Exception $err){ 
            $response = [
                'status' => 'OK',
                'code'  => 109,
                'msg' => $err->getMessage(),
            ];
            error_log($err->getMessage());
        }
        return $datajson->setData(array('result'=>$response));
    }
    protected function validateEmailAvailable($email){
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('This email address is already assigned to another user.')
            );
        }
    }
    protected function validateEmailFormatXpec($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        }else{
            return true;
        }
    }
}