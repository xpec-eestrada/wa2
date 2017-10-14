<?php
/**
 * Magiccart 
 * @category  Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license   http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2016-02-28 10:10:00
 * @@Modify Date: 2016-06-08 15:00:19
 * @@Function:
 */
namespace Magiccart\Alothemes\Block;

class Themecfg extends \Magento\Framework\View\Element\Template
{

    public $_themeCfg;
    public $_time;

    public $_scopeConfig;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $time,
        \Magiccart\Alothemes\Helper\Data $_helper,
        array $data = []
	) {
        parent::__construct($context, $data);
        $this->_time  		= $time;
		$this->_themeCfg 	= $_helper->getThemeCfg();
		$this->_scopeConfig = $context->getScopeConfig();
	}

	public function getThemecfg()
	{
		// $cfg = $this->_themeCfg;
		// $html ='';
		// $font 	= $cfg['font'];
		// /* get Lib Font */
		// if($font['google']) $html  = '<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family='.$font['google'].'" media="all" />';
		// $html  .= "\n"; // break line;
		// /* CssGenerator */
		// $html  .= '<style type="text/css">';
		// $html  .= '*, body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6{ font-size: '.$font['size'].'; font-family: '.$font['google'].';}';

		// $design = $this->_scopeConfig->getValue( 'alodesign', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
		// foreach ($design as $group => $options) 
		// {
		// 	foreach ($options as $property => $values) {
		// 		$values = @unserialize($values);
		// 		if(!$values) continue;
		// 		foreach ($values as $value) {
		// 			if(!$value) continue;
		// 			$html .= htmlspecialchars_decode($value['selector']) .'{';
		// 				$html .= $value['color'] 		? 'color:' .$value['color']. ';' 					: '';
		// 				$html .= $value['background'] 	? ' background-color:' .$value['background']. ';' 	: '';
		// 				$html .= $value['border'] 		? ' border-color:' .$value['border']. ';' 			: '';
		// 			$html .= '}';
		// 		}
		// 	}
		// }
		// $html  .= '</style>';
		// $html  .= "\n"; // break line;
		// $cfg['general']['baseUrl'] = $this->getBaseUrl();
		// $optRm = array('font', 'grid', 'related', 'upsell', 'crosssell', 'labels', 'timer', 'categorysearch');
		// foreach ($optRm as $opt) { unset($cfg[$opt]); }
		// $html .= '<script type="text/javascript"> Themecfg = '.json_encode($cfg).'</script>';  // json config theme
		// return $html;
	}

}
