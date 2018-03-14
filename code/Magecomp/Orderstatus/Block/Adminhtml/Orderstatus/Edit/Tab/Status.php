<?php
namespace Magecomp\Orderstatus\Block\Adminhtml\Orderstatus\Edit\Tab;

use \Psr\Log\LoggerInterface;

use Magecomp\Orderstatus\Model\OrderstatusemailFactory;

class Status extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	protected $_systemStore;
	protected $emailcollection;
	protected $statusemailFactory;
	
	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Magento\Framework\Registry $registry, 
		\Magento\Framework\Data\FormFactory $formFactory, 
		\Magento\Store\Model\System\Store $systemStore, 
		LoggerInterface $logger,
		\Magento\Email\Model\ResourceModel\Template\CollectionFactory $emailcollection,
		OrderstatusemailFactory $statusemailFactory,
		array $data = []
	) 
	{
		$this->logger = $logger;
		$this->_systemStore = $systemStore;
		$this->emailcollection = $emailcollection;
		$this->statusemailFactory = $statusemailFactory;
		parent::__construct($context, $registry, $formFactory, $data);
	}

	protected function _prepareForm()
	{
		/* @var $model \Magento\Cms\Model\Page */
		$model = $this->_coreRegistry->registry('orderstatus');
		
		/*
		* Checking if user have permissions to save information
		*/
		if ($this->_isAllowedAction('Magecomp_Orderstatus::orderstatus')) {
			$isElementDisabled = false;
		} else {
			$isElementDisabled = true;
		}

		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create();
		
		$form->setHtmlIdPrefix('orderstatus_');
		
		$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Status Information')]);
		
		if ($model->getId()) 
		{
			$fieldset->addField('orderstatus_id', 'hidden', ['name' => 'orderstatus_id']);
		}
		
		$fieldset->addField(
		'order_status',
		'text',
		[
			'name' => 'order_status',
			'label' => __('Title'),
			'title' => __('Title'),
			'required' => true,
			'disabled' => $isElementDisabled
		]
		);
		
		$fieldset->addField(
		'order_is_active',
		'select',
		[
			'name' => 'order_is_active',
			'label' => __('Active'),
			'title' => __('Active'),
			'required' => false,
			'options' => $model->getAvailableoption(),
			'disabled' => $isElementDisabled
		]
		);
				
		$field = $fieldset->addField(
                'order_parent_state',
				'multiselect',
                [
                    'name' => 'order_parent_state[]',
                    'label' => __('Select State to Bind with Order Status'),
                    'title' => __('Select State to Bind with Order Status'),
                    'required' => false,
					'values' => $model->getAvailablestateoption(),
                    'disabled' => $isElementDisabled
                ]
        	);
			$renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
		
		$fieldset->addField(
		'order_notify_by_email',
		'select',
		[
			'name' => 'order_notify_by_email',
			'label' => __('Send Email Notifications to Customers'),
			'title' => __('Send Email Notifications to Customers'),
			'required' => false,
			'options' => $model->getAvailableoption(),
			'disabled' => $isElementDisabled
		]
		);
		
		/* For Getting All Store View List*/
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
		$storeViews = $storeManager->getStores($withDefault = false);
		
		$emailCollection = $this->emailcollection->create();
		
		$optionscol = [0 => 'Order Status Change Default Template From Locale'];
		foreach($emailCollection as $curemail)
		{
			$optionscol[$curemail->getTemplateId()] = $curemail->getTemplateCode();	
		}
		
		
		
		foreach ($storeViews as $storeView)
        {
			$fieldset->addField(
			'store_template[' . $storeView->getStoreId() . ']',
			'select',
			[
				'name' => 'store_template[' . $storeView->getStoreId() . ']',
				'label' => $storeView->getName().' Email Template',
				'title' => $storeView->getName().' Email Template',
				'required' => true,
				'options' => $optionscol,
				'disabled' => $isElementDisabled
			]
			);
			
			//set value when edit record
			if ($model->getId()) 
			{
				$realid = 0;
				$statusemailmodel =  $this->statusemailFactory->create();
			    $emailcollection = $statusemailmodel->getCollection()
								 ->addFieldToFilter('order_status_id',$model->getId())
								 ->addFieldToFilter('order_store_id',$storeView->getStoreId());
			   foreach($emailcollection as $curemail)
			   {
					   $realid = $curemail->getOrderTemplateId(); 
			   }
			   
			   $model->setData('store_template[' . $storeView->getStoreId() . ']',$realid);
			}
        }
		
	
		
		
		
		$this->_eventManager->dispatch('adminhtml_orderstatus_edit_tab_status_prepare_form', ['form' => $form]);
		$form->setValues($model->getData());
		$this->setForm($form);
		
		return parent::_prepareForm();
	}

	/**
	* Prepare label for tab
	*
	* @return string
	*/
	public function getTabLabel()
	{
		return __('Status Settings');
	}

	/**
	* Prepare title for tab
	*
	* @return string
	*/
	public function getTabTitle()
	{
		return __('Status Settings');
	}

	public function canShowTab()
	{
		return true;
	}
	
	public function isHidden()
	{
		return false;
	}

	protected function _isAllowedAction($resourceId)
	{
		return $this->_authorization->isAllowed($resourceId);
	}
}