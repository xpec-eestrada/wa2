<?php
namespace Xpectrum\AtributoAdicional\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;
    private $attributeSetFactory;
    protected $_eavConfig;
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        Config $eavConfig
        )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory=$attributeSetFactory;
        $this->_eavConfig = $eavConfig;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try{
            $setup->startSetup();

            /* Attributo Text Rut */
            $code_attribute='rut';
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $attrSet            = $this->attributeSetFactory->create();
            $entity_type        = $this->_eavConfig->getEntityType('customer');
            $entity_type_id     = $entity_type->getId();
            $attribute_set_id   = $entity_type->getDefaultAttributeSetId();
            $attribute_group_id = $attrSet->getDefaultGroupId($attribute_set_id);
            $customerSetup      = $this->customerSetupFactory->create(['setup' => $setup]);
            $order=151;

            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute,  array(
                "type"     => "varchar",
                "label"    => "Rut",
                "backend"  => "Xpectrum\AtributoAdicional\Model\Attribute\Backend\Rut",
                "input"    => "text",
                "visible"  => true,
                "required" => false,
                'user_defined' => true,
                "system"   => 0,
                "position" => $order,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

            ));
            $customerSetup->addAttributeToGroup(
                $entity_type_id,
                $attribute_set_id,
                $attribute_group_id,
                $code_attribute,
                '33'
            );
            $input              = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute);
            $used_in_forms      = array();
            $used_in_forms[]    = "adminhtml_customer";
            $used_in_forms[]    = "checkout_register";
            $used_in_forms[]    = "customer_account_create";
            $used_in_forms[]    = "customer_account_edit";

            $input->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", $order);
            $input->save();


            /* Attributo Text Numero de Contacto */
            $order          = 152;
            $code_attribute = 'numero_contacto';

            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute,  array(
                "type"     => "varchar",
                "label"    => "Número de Contácto",
                "input"    => "text",
                "visible"  => true,
                "required" => false,
                'user_defined' => true,
                "system"   => 0,
                "position" => $order,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

            ));
            $customerSetup->addAttributeToGroup(
                $entity_type_id,
                $attribute_set_id,
                $attribute_group_id,
                $code_attribute,
                '33'
            );
            $input              = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute);
            $used_in_forms      = array();
            $used_in_forms[]    = "adminhtml_customer";
            $used_in_forms[]    = "checkout_register";
            $used_in_forms[]    = "customer_account_create";
            $used_in_forms[]    = "customer_account_edit";

            $input->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", $order);
            $input->save();
            /* Attributo Text Numero de Contacto */

            $setup->endSetup();
        }catch(Exception $err){
            
        }
    }
}
