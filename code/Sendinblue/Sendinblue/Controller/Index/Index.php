<?php
/**
 * @author Sendinblue plateform <contact@sendinblue.com>
 * @copyright  2017-2018 Sendinblue
 * URL:  https:www.sendinblue.com
 * Do not edit or add to this file if you wish to upgrade Sendinblue Magento plugin to newer
 * versions in the future. If you wish to customize Sendinblue magento plugin for your
 * needs then we can't provide a technical support.
 **/
namespace Sendinblue\Sendinblue\Controller\Index;
 
use Magento\Framework\App\Action\Context;

use Sendinblue\Sendinblue\Model;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultFactory;
    public $modelVal;
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory
        )
    {
        $this->resultFactory = $resultPageFactory;
        parent::__construct($context);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->modelVal = $objectManager->create('\Sendinblue\Sendinblue\Model\SendinblueSib');
    }
 
    public function execute()
    {
        $resultPage = $this->resultFactory->create();
        $getValue = $this->getRequest()->getParam('value');
        $userEmail = base64_decode($getValue);
        $this->dubleoptinProcess($userEmail);
    }

    /**
     * Description: get responce and send confirm subscription mail and redirect in given url
     *
     */
    public function dubleoptinProcess($userEmail)
    {
        $nlStatus = $this->modelVal->checkNlStatus($userEmail);
        if (!empty($userEmail) && $nlStatus = 1) {
            $apiKey = $this->modelVal->getDbData('api_key');
            $optinListId = $this->modelVal->getDbData('optin_list_id');
            $listId = $this->modelVal->getDbData('selected_list_data');

            $mailin = $this->modelVal->createObjMailin($apiKey);
            $data = array();
            $data = array( "email" => $userEmail,
                    "attributes" => array(),
                    "blacklisted" => 0,
                    "listid" => array($listId),
                    "listid_unlink" => array($optinListId),
                    "blacklisted_sms" => 0
                );
            $mailin->createUpdateUser($data);
            $confirmEmail = $this->modelVal->getDbData('final_confirm_email');
            if ($confirmEmail === 'yes') {
                $finalId = $this->modelVal->getDbData('final_template_id');
                $this->modelVal->sendOptinConfirmMailResponce($userEmail, $finalId, $apiKey);
            }
        }
        $doubleoptinRedirect = $this->modelVal->getDbData('doubleoptin_redirect');
        $optinUrlCheck = $this->modelVal->getDbData('optin_url_check');
        if ($optinUrlCheck === 'yes' && !empty($doubleoptinRedirect)) {
            header("Location: ".$doubleoptinRedirect);
            ob_flush_end();
        } else {
            $shopName = $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
            header("Location: ".$shopName);
            ob_flush_end();
        }
    }
}
