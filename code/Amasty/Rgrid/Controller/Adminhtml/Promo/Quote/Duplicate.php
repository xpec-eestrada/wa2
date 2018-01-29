<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rgrid
 */

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

class Duplicate extends \Amasty\Rgrid\Controller\Adminhtml\Promo\Quote
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $collection = $this->_collectionFactory->create();
                $rule = $collection->getItemById($id);
                $rule->setId(null);
                $rule->save();
                $this->messageManager->addSuccessMessage(__('The rule has been duplicated.'));
                $this->_redirect('sales_rule/*/edit', ['id' => $rule->getId()]);
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t duplicate the rule right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('sales_rule/*/');
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to delete.'));
        $this->_redirect('sales_rule/*/');
    }
}
