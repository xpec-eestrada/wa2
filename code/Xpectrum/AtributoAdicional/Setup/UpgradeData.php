<?php
namespace Xpectrum\AtributoAdicional\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;


class UpgradeData implements UpgradeDataInterface
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
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try{
            if ( version_compare( $context->getVersion(), '1.0.1', '<' ) ) {
                $setup->startSetup();

                $code_attribute     = 'xpec_prefijo_telefono';
                $attrSet            = $this->attributeSetFactory->create();
                $entity_type        = $this->_eavConfig->getEntityType('customer_address');
                $entity_type_id     = $entity_type->getId();
                $attribute_set_id   = $entity_type->getDefaultAttributeSetId();
                $attribute_group_id = $attrSet->getDefaultGroupId($attribute_set_id);
                $customerSetup      = $this->customerSetupFactory->create(['setup' => $setup]);
                $order=102;


                /* Attributo Prefijo Numero de contcto */
                $customerSetup->addAttribute('customer_address', $code_attribute,  array(
                    "type"     => "varchar",
                    "label"    => "Prefijo Teléfono",
                    "input"    => "text",
                    "visible"  => true,
                    "required" => false,
                    "system"   => 0,
                    'user_defined' => true,
                    'sort_order' => $order,
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
    
                $input              = $customerSetup->getEavConfig()->getAttribute('customer_address', $code_attribute);
                $used_in_forms      = array();
                $used_in_forms[]    = "adminhtml_customer_address";
                $used_in_forms[]    = "customer_address_edit";
                $used_in_forms[]    = "customer_register_address";
    
                $input->setData("used_in_forms", $used_in_forms)
                    ->setData("is_used_for_customer_segment", true)
                    ->setData("is_system", 0)
                    ->setData("is_user_defined", 1)
                    ->setData("is_visible", 1)
                    ->setData("sort_order", $order);
                $input->save();
    
    
                /* Attributo Prefijo Numero de contcto */
    
                $setup->endSetup();
            }
            
        }catch(Exception $err){
            
        }
    }
}
