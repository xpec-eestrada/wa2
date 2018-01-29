<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rgrid
 */

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

class MassStatus extends \Amasty\Rgrid\Controller\Adminhtml\Promo\Quote
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('ids');
        $status = $this->getRequest()->getParam('status');

        if ($ids) {
            try {
                $collection = $this->_collectionFactory->create();
                $collection->addFieldToFilter('rule_id', ['in' => $ids ]);
                foreach ($collection as $rule) {
                    $rule->setIsActive($status);
                    $rule->save();
                }
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been updated.', count($collection))
                );
                $this->_redirect('sales_rule/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while updating the rule(s) status.')
                );
            }
        }
        $this->_redirect('sales_rule/*/');
    }
}
