<?php
/**
 * Yireo TaxRatesManager extension for Magento 1
 *
 * @package     Yireo_TaxRatesManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

use Yireo_TaxRatesManager_Config_Config as Config;
use Yireo_TaxRatesManager_Api_LoggerInterface as Logger;
use Yireo_TaxRatesManager_Check_Check as Check;
use Mage_Core_Model_Store as Store;

/**
 * Class Yireo_TaxRatesManager_Model_Cron_Check
 */
class Yireo_TaxRatesManager_Model_Cron_Check
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Check
     */
    private $check;

    /**
     * Yireo_TaxRatesManager_Model_Cron constructor.
     */
    public function __construct()
    {
        $factory = new Yireo_TaxRatesManager_Object_Factory();
        $this->config = $factory->getConfig();
        $this->logger = $factory->getLogger();
        $this->check = $factory->getCheck();
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Http_Client_Exception
     */
    public function execute(): bool
    {
        ob_start();
        $this->check->execute();
        $contents = ob_get_clean();

        if (!$contents) {
            return false;
        }

        if ($this->config->sendEmail() === false) {
            return false;
        }

        // @todo: Rewrite this into a transactional email
        $subject = 'Yireo_TaxRateManager: Found warnings';
        $senderName = Mage::getStoreConfig(Store::XML_PATH_STORE_STORE_NAME);
        $senderEmail = 'info@yireo.com';

        /** @var Mage_Core_Model_Email $mail */
        $mail = Mage::getModel('core/email');
        $mail->setToName($senderName);
        $mail->setToEmail($senderEmail);
        $mail->setBody($contents);
        $mail->setSubject($subject);
        $mail->setFromEmail($senderEmail);
        $mail->setFromName($senderName);
        $mail->setType('text');
        $mail->send();

        return true;
    }
}
