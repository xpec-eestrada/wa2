<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rgrid
 */

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

class MassDelete extends \Amasty\Rgrid\Controller\Adminhtml\Promo\Quote
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('ids');
        if ($ids && is_array($ids)) {
            try {
                /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $collection */
                $collection = $this->_collectionFactory->create();
                $collection->addFieldToFilter('rule_id', ['in' => $ids ]);
                foreach ($collection as $rule) {
                    $rule->delete();
                }
                $this->messageManager->addSuccessMessage(__('You deleted %1 rule(s).', count($collection)));
                $this->_redirect('sales_rule/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the rule right now. Please review the log and try again.')
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
