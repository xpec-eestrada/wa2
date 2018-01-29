<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rgrid
 */

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

class MassPriority extends \Amasty\Rgrid\Controller\Adminhtml\Promo\Quote
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('ids');
        $priority = $this->getRequest()->getParam('priority');

        $collection = $this->_collectionFactory->create();
        if ($priority == 'low') {
            $rulePriority = $collection->setOrder('sort_order', 'DESC')->setPageSize(1)->getFirstItem()->getSortOrder();
            $rulePriority++;
        } else {
            $rulePriority = $collection->setOrder('sort_order', 'ASC')->setPageSize(1)->getFirstItem()->getSortOrder();
            if ($rulePriority != 0) {
                $rulePriority--;
            }
        }

        if ($ids) {
            try {
                /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $collection */
                $collection = $this->_collectionFactory->create();
                $collection->addFieldToFilter('rule_id', ['in' => $ids ]);
                /** @var \Magento\SalesRule\Model\Rule $rule */
                foreach ($collection as $rule) {
                    $rule->setSortOrder($rulePriority);
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
