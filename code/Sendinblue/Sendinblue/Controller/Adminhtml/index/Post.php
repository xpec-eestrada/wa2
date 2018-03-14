<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sendinblue\Sendinblue\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

use Sendinblue\Sendinblue\Model;

class Post extends \Magento\Backend\App\Action
{
    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $model = $this->sibObject();
            if (isset($post['submitUpdate']) && $post['submitUpdate'] == 'Update') {
                $this->apiKeyPostProcessConfiguration();
            }

            if (isset($post['submitForm2']) && $post['submitForm2'] == 'Update') {
                $this->saveTemplateValue();
                // update template id configuration.
            }

            if (isset($post['submitUpdateImport']) && $post['submitUpdateImport'] == 'Import Old Subscribers') {
                $listId = $model->getDbData('selected_list_data');
                $resp = $model->sendAllMailIDToSendin($listId);
                if ($resp == 0) {
                    $this->messageManager->addSuccess(__('Old subscribers imported successfully'));
                    $this->_redirect('sendinblue/sib/index');
                    return;
                } else {
                    $this->messageManager->addError(__('Old subscribers not imported successfully, please click on Import Old Subscribers button to import them again'));
                    $this->_redirect('sendinblue/sib/index');
                    return;
                }
            }
            //save value for notify email
            if (isset($post['notify_sms_mail']) && $post['notify_sms_mail'] == 'Save') {
                $this->saveNotifyValue();
            }

            //save order sms send and body details
            if (isset($post['sender_order_save']) && $post['sender_order_save'] == 'Save') {
                $this->saveOrderSms();
            }

            //save shipped sms send and body details
            if (isset($post['sender_shipment_save']) && $post['sender_shipment_save'] == 'Save') {
                $this->saveShippedSms(); 
            }

            /**
             * Description: send single and multi user campaign for subcribe and All Users.
             *
             */
            if (isset($post['sender_campaign_save']) && ($post['sender_campaign_save'] == 'Send the campaign')) {
                $returnResp = $this->sendSmsCampaign();
                if ($returnResp == 'success') {
                    $this->messageManager->addSuccess(__('Campaign has been scheduled successfully'));
                    $this->_redirect('sendinblue/sib/index');
                    return;
                } else {
                    $this->messageManager->addError(__('Campaign failed'));
                    $this->_redirect('sendinblue/sib/index');
                    return;
                }
            }
            /**
             * Description: send test email if smtp setup well.
             *
             */
            if (isset($post['sendTestMail']) && $post['sendTestMail'] == 'Send') {
                $post = $this->getRequest()->getPostValue();
                $userEmail = !empty($post['testEmail']) ? $post['testEmail'] : '';
                $relayData = $model->getDbData('relay_data_status');
                if (!empty($userEmail) && $post['smtpservices'] == 1) {
                    if ($relayData == 'enabled') {
                        $title = __('[SendinBlue SMTP] test email');
                        $tempName = 'sendinsmtp_conf';
                        $respMail = $model->smtpSendMail($userEmail, $title, $tempName, $paramVal = '');
                        if ($respMail['status'] == 1) {
                            $this->messageManager->addSuccess(__('Mail sent'));
                            $this->_redirect('sendinblue/sib/index');
                            return;
                        } else {
                            $this->messageManager->addError(__('Mail not sent'));
                            $this->_redirect('sendinblue/sib/index');
                            return;
                        }
                    } else {
                        $this->messageManager->addError(__('Your SMTP account is not activated and therefore you can\'t use SendinBlue SMTP. For more informations, Please contact our support to: contact@sendinblue.com'));
                        $this->_redirect('sendinblue/sib/index');
                        return;
                    }
                } else {
                    $this->messageManager->addError(__('Put valid email'));
                    $this->_redirect('sendinblue/sib/index');
                    return;
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process your request right now.')
            );
            $this->_redirect('sendinblue/sib/index');
            return;
        }
    }

    public function apiKeyPostProcessConfiguration()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->_redirect('sendinblue/sib/index');
            return;
        }
        try {
            $model = $this->sibObject();

            $error = false;
            if (!\Zend_Validate::is($post['apikey'], 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['status']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['submitUpdate']), 'NotEmpty')) {
                $error = true;
            }
            if ($error) {
                throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($error->getMessage()), $error);
            }
            $apiKey = trim($post['apikey']);
            $status = trim($post['status']);
            $storeID = $model->storeId;
            if ($status == 1) {
                $apikey = trim($post['apikey']);
                $rowList = $model->checkApikey($apikey);

                if (isset($rowList) && $rowList['code'] == 'success' && $rowList['message'] == 'Data retrieved') {
                    //If a user enters a new API key, we remove all records that belongs to the
                    //old API key.
                    $oldApiKey = trim($model->getValueDefault->getValue('sendinblue/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

                    // Old key
                    if ($apikey != $oldApiKey) {
                        // Reset data for old key
                        $model->resetDataBaseValue();
                        $model->resetSmtpDetail();
                    }

                    if (isset($apikey)) {
                        $model->resConfig->saveConfig('sendinblue/api_key', $apikey, $model->scopeTypeDefault, $model->storeId);
                    }

                    if (isset($status)) {
                        $model->resConfig->saveConfig('sendinblue/api_key_status', $status, $model->scopeTypeDefault, $model->storeId);
                    }

                    $sendinListdata = $model->getValueDefault->getValue('sendinblue/selected_list_data', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $sendinFirstrequest = $model->getValueDefault->getValue('sendinblue/first_request', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    
                    if (empty($sendinListdata) && empty($sendinFirstrequest)) {
                        $model->resConfig->saveConfig('sendinblue/first_request', 1, $model->scopeTypeDefault, $model->storeId);
                        $model->resConfig->saveConfig('sendinblue/subscribe_setting', 1, $model->scopeTypeDefault, $model->storeId);
                        $model->resConfig->saveConfig('sendinblue/notify_cron_executed', 0, $model->scopeTypeDefault, $model->storeId);
                        $model->resConfig->saveConfig('sendinblue/syncronize', 1, $model->scopeTypeDefault, $model->storeId);

                        $model->createFolderName($apikey);
                    }
                    $this->messageManager->addSuccess(
                        __('Sendiblue configuration setting Successfully updated')
                    );
                    $this->_redirect('sendinblue/sib/index');
                    return;
                } else {
                    //We reset all settings  in case the API key is invalid.
                    $model->resConfig->saveConfig('sendinblue/api_key_status', 0, $model->scopeTypeDefault, $model->storeId);
                    $model->resetDataBaseValue();
                    $this->messageManager->addError(
                        __('API key is invalid.')
                    );
                    $this->_redirect('sendinblue/sib/index');
                    return;
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('API key is invalid.')
            );
            $this->_redirect('sendinblue/sib/index');
            return;
        }
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    public function sibObject()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('Sendinblue\Sendinblue\Model\SendinblueSib');
    }

    public function saveTemplateValue()
    {
        $model = $this->sibObject();
        $post = $this->getRequest()->getPostValue();
        $valueTemplateId = !empty($post['template']) ? $post['template'] : '';
        $doubleOptinTempId = !empty($post['doubleoptin_template_id']) ? $post['doubleoptin_template_id'] : '';
        $subscribeConfirmType = !empty($post['subscribe_confirm_type']) ? $post['subscribe_confirm_type'] : '';
        $optinRedirectUrlCheck = !empty($post['optin_redirect_url_check']) ? $post['optin_redirect_url_check'] : '';
        $doubleoptinRedirectUrl = !empty($post['doubleoptin_redirect_url']) ? $post['doubleoptin_redirect_url'] : '';
        $finalConfirmEmail = !empty($post['final_confirm_email']) ? $post['final_confirm_email'] : '';
        $finalTempId = !empty($post['template_final']) ? $post['template_final'] : '';
        $shopApiKey = $model->getDbData('api_key');

        $model->updateDbData('doubleoptin_template_id', $doubleOptinTempId);
        $model->updateDbData('template_id', $valueTemplateId);
        $model->updateDbData('optin_url_check', $optinRedirectUrlCheck);
        $model->updateDbData('doubleoptin_redirect', $doubleoptinRedirectUrl);
        $model->updateDbData('final_confirm_email', $finalConfirmEmail);
        if (!empty($finalTempId)) {
            $model->updateDbData('final_template_id', $finalTempId);
        }
        $model->updateSender();
        if (!empty($subscribeConfirmType)) {
            $model->updateDbData('confirm_type', $subscribeConfirmType);
            if ($subscribeConfirmType == 'doubleoptin') {
                $resOptin = $model->checkFolderListDoubleoptin($shopApiKey);
                if (!empty($resOptin['optin_id'])) {
                    $model->updateDbData('optin_list_id', $resOptin['optin_id']);
                }

                if ($resOptin === false) {
                    $mailin = $model->createObjMailin($shopApiKey);
                    if (!empty($shopApiKey)) {
                        $data = [];
                        $data = ["name"=> "FORM"];
                        $folderRes = $mailin->createFolder($data);
                        $folderId = $folderRes['data']['id'];
                    }

                    if (!empty($shopApiKey)) {
                        $data = [];
                        $data = [
                          "list_name" => 'Temp - DOUBLE OPTIN',
                          "list_parent" => $folderId
                        ];
                        $listResp = $mailin->createList($data);
                        $listId = $listResp['data']['id'];
                        $model->updateDbData('optin_list_id', $listId);
                    }
                }
            }
        }
        $displayList = $post['display_list'];
        if (!empty($displayList)) {
            if ($model->getDbData('subscribe_setting') == 1) {
                $listValue = implode('|', $displayList);
                $model->updateDbData('selected_list_data', $listValue);
            } else {
                $model->updateDbData('subscribe_setting', 0);
            }
        }
        $this->messageManager->addSuccess(__('Sendiblue configuration setting Successfully updated'));
        $this->_redirect('sendinblue/sib/index');
        return true;
    }

    /**
     * Description: Save sms limit warning details in DB
     *
     */
    public function saveNotifyValue()
    {
        $post = $this->getRequest()->getPostValue();
        $model = $this->sibObject();
        if (!empty($post['value_notify_email']) && !empty($post['notify_value'])) {
            $model->updateDbData('notify_email', $post['value_notify_email']);
            $model->updateDbData('notify_value', $post['notify_value']);
            $model->updateDbData('notify_email_send', 0);
        }
        $this->messageManager->addSuccess(__('Sendiblue configuration setting Successfully updated'));
        $this->_redirect('sendinblue/sib/index');
        return true;
    }

    /**
     * Description: Save sms Order confirmation sender and body.
     *
     */
    public function saveOrderSms()
    {
        $post = $this->getRequest()->getPostValue();
        $model = $this->sibObject();
        if (!empty($post['sender_order']) && !empty($post['sender_order_message'])) {
            $model->updateDbData('sender_order', $post['sender_order']);
            $model->updateDbData('sender_order_message', $post['sender_order_message']);
        }
        $this->messageManager->addSuccess(__('Sendiblue configuration setting Successfully updated'));
        $this->_redirect('sendinblue/sib/index');
        return true;
    }

    /**
     * Description: Save sms Order shipped sender and body.
     *
     */
    public function saveShippedSms()
    {
        $post = $this->getRequest()->getPostValue();
        $model = $this->sibObject();
        if (!empty($post['sender_shipment']) && !empty($post['sender_shipment_message'])) {
            $model->updateDbData('sender_shipment', $post['sender_shipment']);
            $model->updateDbData('sender_shipment_message', $post['sender_shipment_message']);
        }
        $this->messageManager->addSuccess(__('Sendiblue configuration setting Successfully updated'));
        $this->_redirect('sendinblue/sib/index');
        return true;
    }

    /**
     * Description: This method is called when the user sets the Campaign Sms and hits the submit button.
     */
    public function sendSmsCampaign()
    {
        $post = $this->getRequest()->getPostValue();
        $sendinSmsChoice = $post['Sms_Choice'];
        $model = $this->sibObject();
        if (!empty($post)) {
            if ($sendinSmsChoice == 1) {
               return $result = $model->singleChoiceCampaign($post);
            } elseif ($sendinSmsChoice == 0) {
                return $result = $model->multipleChoiceCampaign($post);
            } elseif ($sendinSmsChoice == 2) {
               return $result = $model->multipleChoiceSubCampaign($post);
            }
        }
    }
}
