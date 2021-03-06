<?php

/**
 * Log
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */

/**
 * Log
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Log extends Mage_Core_Helper_Abstract
{


    /**
     * This method writes log message to modules log file
     * and system.log
     *
     * @param mixed  $message
     * @param string $method
     * @param string $line
     */
    public function log(
        $message,
        $method = null,
        $line = null,
        $level = Zend_Log::DEBUG
    )
    {
        $currentLevel = \Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
        );

        // presuming we use only INFO & DEBUG levels
        // if we have logging on INFO, then log only with level == INFO
        if ($currentLevel == Zend_Log::INFO && $level != Zend_Log::INFO) {
            return;
        }
        if (is_null($method)) {
            $method = __METHOD__;
        }
        if (is_null($line)) {
            $line = __LINE__;
        }
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
            $method = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
            $line = $backtrace[0]['line'];
        }
        $message = print_r($message, true);
        if (strlen($message) > 1024) {
            $message = substr($message, 0, 1024) . ' ...';
        }
        Mage::log(
            sprintf('%s(%s): %s', $method, $line, $message),
            null,
            'mageflow.log'
        );
    }

}
