<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */

namespace Amasty\RulesPro\Model\Rule\Condition;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Rule\Model\Condition as Condition;
use Magento\Customer\Model\Address;

/**
 * Product rule condition data model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    private $resource;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    public function __construct(
        Condition\Context $context,
        \Magento\Customer\Model\ResourceModel\Customer $resource,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve attribute object
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    public function getAttributeObject()
    {
        return $this->resource->getAttribute($this->getAttribute());
    }

    public function loadAttributeOptions()
    {
        $customerAttributes = $this->resource
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = [];
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        foreach ($customerAttributes as $attribute) {
            if (!($attribute->getFrontendLabel()) || !($attribute->getAttributeCode())) {
                continue;
            }

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        $this->_addSpecialAttributes($attributes);
        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['id'] = __('Customer ID');
        $attributes['membership_days'] = __('Membership Days');
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * This value will define which operators will be available for this condition.
     *
     * Possible values are: string, numeric, date, select, multiselect, grid, boolean
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() == 'entity_id' || $this->getAttribute() == 'membership_days') {
            return 'string';
        }
        $customerAttribute = $this->getAttributeObject();
        if (!$customerAttribute) {
            return parent::getInputType();
        }

        return $this->getInputTypeFromAttribute($customerAttribute);
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $customerAttribute
     *
     * @return string
     */
    protected function getInputTypeFromAttribute($customerAttribute)
    {
        if (!is_object($customerAttribute)) {
            $customerAttribute = $this->getAttributeObject();
        }
        $possibleTypes = ['string', 'numeric', 'date', 'select', 'multiselect', 'grid', 'boolean'];
        if (in_array($customerAttribute->getFrontendInput(), $possibleTypes)) {
            return $customerAttribute->getFrontendInput();
        }
        switch ($customerAttribute->getFrontendInput()) {
            case 'gallery':
            case 'media_image':
            case 'selectimg': // amasty customer attribute
                return 'select';
            case 'multiselectimg': // amasty customer attribute
                return 'multiselect';
        }

        return 'string';
    }

    public function getValueElement()
    {
        $element = parent::getValueElement();
        switch ($this->getInputType()) {
            case 'date':
                $element->setClass('hasDatepicker');
                break;
        }

        return $element;
    }

    public function getExplicitApply()
    {
        return ($this->getInputType() == 'date');
    }

    /**
     * Value element type will define renderer for condition value element
     *
     * @see \Magento\Framework\Data\Form\Element
     * @return string
     */
    public function getValueElementType()
    {
        $customerAttribute = $this->getAttributeObject();

        if ($this->getAttribute() === 'entity_id' || $this->getAttribute() == 'membership_days') {
            return 'text';
        }
        if (!is_object($customerAttribute)) {
            return parent::getValueElementType();
        }

        $availableTypes = [
            'checkbox',
            'checkboxes',
            'date',
            'editablemultiselect',
            'editor',
            'fieldset',
            'file',
            'gallery',
            'image',
            'imagefile',
            'multiline',
            'multiselect',
            'radio',
            'radios',
            'select',
            'text',
            'textarea',
            'time'
        ];

        if (in_array($customerAttribute->getFrontendInput(), $availableTypes)) {
            return $customerAttribute->getFrontendInput();
        }
        switch ($customerAttribute->getFrontendInput()) {
            case 'selectimg':
            case 'boolean':
                return 'select';
            case 'multiselectimg':
                return 'multiselect';
        }

        return parent::getValueElementType();
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        $selectOptions = [];
        $attributeObject = $this->getAttributeObject();

        if (is_object($attributeObject) && $attributeObject->usesSource()) {
            $addEmptyOption = true;
            if ($attributeObject->getFrontendInput() == 'multiselect') {
                $addEmptyOption = false;
            }
            $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
        }

        $key = 'value_select_options';

        if (!$this->hasData($key)) {
            $this->setData($key, $selectOptions);
        }

        return $this->getData($key);
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $customer = $model;
        if (!$customer instanceof \Magento\Customer\Model\Customer) {
            $customer = $model->getQuote()->getCustomer();
            $attr     = $this->getAttribute();

            $allAttr = $customer->__toArray();

            if ($attr == 'membership_days') {
                $allAttr[$attr] = $this->getMembership($customer->getCreatedAt());
            }
            if ($attr != 'entity_id' && !array_key_exists($attr, $allAttr)) {
                $address        = $model->getQuote()->getBillingAddress();
                $allAttr[$attr] = $address->getData($attr);
            }
            $customer = $this->customerFactory->create()->setData($allAttr);
        }

        return parent::validate($customer);
    }

    public function getMembership($created)
    {
        return round((time() - strtotime($created)) / 60 / 60 / 24);
    }
}
