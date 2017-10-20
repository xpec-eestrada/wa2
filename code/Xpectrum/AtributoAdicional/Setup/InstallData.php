<?php
namespace Xpectrum\AtributoAdicional\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
        )
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try{
            $setup->startSetup();

            /* Attributo Text Rut */
            $code_attribute='rut';
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute,  array(
                "type"     => "varchar",
                "label"    => "Rut",
                "input"    => "text",
                "visible"  => true,
                "required" => false,
                "system"   => 0,
                "position" => 151

            ));
            $attribute = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute);
            $used_in_forms=array();
            $used_in_forms[]="adminhtml_customer";
            $used_in_forms[]="checkout_register";
            $used_in_forms[]="customer_account_create";
            $used_in_forms[]="customer_account_edit";
            $attribute->setData("used_in_forms", $used_in_forms);
            $attribute->save();
            /* Attributo Text Rut */

            /* Attributo Text Numero de Contacto */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $code_attribute='numero_contacto';
            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute,  array(
                "type"     => "varchar",
                "label"    => "Número de Contácto",
                "input"    => "text",
                "visible"  => true,
                "required" => false,
                "system"   => 0,
                "position" => 152

            ));
            $attribute = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, $code_attribute);
            $used_in_forms=array();
            $used_in_forms[]="adminhtml_customer";
            $used_in_forms[]="checkout_register";
            $used_in_forms[]="customer_account_create";
            $used_in_forms[]="customer_account_edit";
            $attribute->setData("used_in_forms", $used_in_forms);
            $attribute->save();
            /* Attributo Text Numero de Contacto */

            $setup->endSetup();
        }catch(Exception $err){
            
        }
    }
}
