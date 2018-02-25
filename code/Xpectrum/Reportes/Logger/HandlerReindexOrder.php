<?php
namespace Xpectrum\Reportes\Logger;

use Monolog\Logger;

class HandlerReindexOrder extends \Magento\Framework\Logger\Handler\Base
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
    protected $fileName = '/var/log/xpec_reindex_order.log';
}