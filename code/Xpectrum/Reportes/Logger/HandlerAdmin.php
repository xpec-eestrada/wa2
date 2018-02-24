<?php
namespace Xpectrum\Reportes\Logger;

use Monolog\Logger;

class HandlerAdmin extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/xpec_order_dashboard.log';
}