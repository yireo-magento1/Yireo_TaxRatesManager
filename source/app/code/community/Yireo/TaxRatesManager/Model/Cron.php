<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Model_Cron
 */
class Yireo_TaxRatesManager_Model_Cron
{
    /**
     * @throws Zend_Http_Client_Exception
     */
    public function execute()
    {
        $logger = new Yireo_TaxRatesManager_Logger_Console();
        $check = new Yireo_TaxRatesManager_Check_Check($logger);

        ob_start();
        $check->execute();
        $contents = ob_get_clean();

        if (!$contents) {
            return;
        }

        // @todo: Rewrite this into a transactional email
        $subject = 'Yireo_TaxRateManager: Found warnings';
        $senderName = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
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
    }
}