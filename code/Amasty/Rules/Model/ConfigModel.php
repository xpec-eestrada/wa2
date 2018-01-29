<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Model;

class ConfigModel
{
    const MODULE = 'amrules';
    const SECTION_GENERAL = 'general';
    const SECTION_SKIP_PRICE = 'skip_price';
    const FIELD_SKIP_SPECIAL_PRICE = 'skip_special_price';
    const FIELD_SKIP_SPECIAL_PRICE_CONFIGURABLE = 'skip_special_price_configurable';
    const FIELD_SKIP_TIER_PRICE = 'skip_tier_price';
    const FIELD_OPTIONS_VALUE = 'options_value';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getOptionsValue()
    {
        return $this->getConfigValue(self::SECTION_GENERAL, self::FIELD_OPTIONS_VALUE);
    }

    /**
     * @return mixed
     */
    public function getSkipSpecialPrice()
    {
        return $this->getConfigValue(self::SECTION_SKIP_PRICE, self::FIELD_SKIP_SPECIAL_PRICE);
    }

    /**
     * @return mixed
     */
    public function getSkipTierPrice()
    {
        return $this->getConfigValue(self::SECTION_SKIP_PRICE, self::FIELD_SKIP_TIER_PRICE);
    }

    /**
     * @return mixed
     */
    public function getSkipSpecialPriceConfigurable()
    {
        return $this->getConfigValue(self::SECTION_SKIP_PRICE, self::FIELD_SKIP_SPECIAL_PRICE_CONFIGURABLE);
    }

    /**
     * @param $section
     * @param $field
     * @param string $module
     * @return mixed
     */
    private function getConfigValue($section, $field, $module = self::MODULE)
    {
        return $this->scopeConfig->getValue(
            $module . '/' . $section . '/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}