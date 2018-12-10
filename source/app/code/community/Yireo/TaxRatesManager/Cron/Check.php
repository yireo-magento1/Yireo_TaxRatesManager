<?php
declare(strict_types=1);

use Yireo_TaxRatesManager_Config_Config as Config;
use Yireo_TaxRatesManager_Api_LoggerInterface as Logger;
use Yireo_TaxRatesManager_Check_Check as Check;
use Mage_Core_Model_Store as Store;

/**
 * Class Yireo_TaxRatesManager_Cron_Check
 */
class Yireo_TaxRatesManager_Cron_Check
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
    public function __construct(
        Config $config,
        Logger $logger,
        Check $check
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->check = $check;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        if ($this->config->sendEmail() === false) {
            return false;
        }

        ob_start();
        callback($this->check); // @todo: Is this still working?
        $contents = ob_get_clean();

        if (!$contents) {
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
