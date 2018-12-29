<?php
/**
 * Yireo TaxRatesManager for Magento
 *
 * @package     Yireo_TaxRatesManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * TaxRatesManager admin controller
 *
 * @category   TaxRatesManager
 * @package     Yireo_TaxRatesManager
 */
class Yireo_TaxRatesManager_TaxratesmanagerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Adminhtml_Model_Session
     */
    protected $adminHtmlSession;

    /**
     * Delete action
     */
    public function fixAction()
    {
        $id = $this->getRequest()->getParam('id');
        $check = $this->getFactory()->getCheck();
        $storedRate = $this->getFactory()->getStoredRatesProvider()->getRateById($id);
        $onlineRates = $this->getFactory()->getOnlineRatesProvider()->getRates();

        $check->checkStoredRate($storedRate, $onlineRates);

        // Set a message
        $this->getAdminHtmlSession()->addSuccess($this->__('Deleted rule succesfully'));

        // Redirect
        $this->_redirect('adminhtml/tax_rate');
    }

    /**
     * Verify if this action is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return Yireo_TaxRatesManager_Object_Factory
     */
    protected function getFactory()
    {
        return Yireo_TaxRatesManager_Object_Factory::getInstance();
    }

    /**
     * Get session
     *
     * @return mixed
     */
    protected function getAdminHtmlSession()
    {
        if (!$this->adminHtmlSession) {
            $this->adminHtmlSession = Mage::getModel('adminhtml/session');
        }

        return $this->adminHtmlSession;
    }
}
