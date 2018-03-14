<?php
namespace Xpectrum\Globals\Block\Category;

use Magento\Framework\View\Element\Template\Context;

class Zoom extends \Magento\Framework\View\Element\Template{
    public function __construct(Context $context,array $data = []){
        parent::__construct($context, $data);
    }
}